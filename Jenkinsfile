pipeline {
    agent any

    environment {
        REMOTE_HOST = "root@192.168.100.60"
        REMOTE_DIR = "/var/www/capstone-project"
        BACKUP_DIR = "/var/www/backup-capstone"
        PHP_BIN = "/usr/bin/php82"
        COMPOSER_BIN = "/usr/local/bin/composer"
        SSH_KEY = "/var/lib/jenkins/.ssh/alpine_git"

        TELEGRAM_TOKEN = credentials('TELEGRAM_TOKEN')
        TELEGRAM_CHAT_ID = credentials('TELEGRAM_CHAT_ID')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                script {
                    env.GIT_COMMIT_MESSAGE = sh(script: 'git log -1 --pretty=%s', returnStdout: true).trim()
                    env.GIT_AUTHOR = sh(script: 'git log -1 --pretty=format:%ae', returnStdout: true).trim()
                }
            }
        }

        stage('Backup Before Deploy') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        if [ -d "${REMOTE_DIR}" ]; then
                            rm -rf ${BACKUP_DIR}
                            cp -r ${REMOTE_DIR} ${BACKUP_DIR}
                        fi
                    '
                """
            }
        }

        stage('Git Pull on Alpine') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        git config --global --add safe.directory ${REMOTE_DIR}
                        if [ ! -d "${REMOTE_DIR}" ]; then
                            git clone https://github.com/bembenk18/capstone-project.git ${REMOTE_DIR}
                        else
                            cd ${REMOTE_DIR} && git pull origin main
                        fi
                    '
                """
            }
        }

        stage('Install Dependencies') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ${COMPOSER_BIN} install --no-interaction --prefer-dist --optimize-autoloader
                    '
                """
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        pkill php-fpm82 || true
                        /usr/sbin/php-fpm82 -D
                    '
                """
            }
        }

        stage('Migrate Database') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} artisan migrate --force
                    '
                """
            }
        }

        stage('Fix Permissions & Cache') {
            steps {
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR}
                        chmod -R guo+w storage bootstrap/cache
                        ${PHP_BIN} artisan cache:clear
                        ${PHP_BIN} artisan config:clear
                        ${PHP_BIN} artisan view:clear
                        ${PHP_BIN} artisan route:clear
                    '
                """
            }
        }
    }

    post {
        success {
            script {
                def msg = """
🚀 *Deployment Sukses*
👤 Commit by: `${env.GIT_AUTHOR}`
📝 Message: `${env.GIT_COMMIT_MESSAGE}`
✅ Status: *BERHASIL*
                """.stripIndent().trim()
                sendTelegram(msg)
            }
        }

        failure {
            script {
                def msg = """
🚨 *Deployment GAGAL*
👤 Commit by: `${env.GIT_AUTHOR}`
📝 Message: `${env.GIT_COMMIT_MESSAGE}`
❌ Status: *GAGAL*
📄 Error: `${currentBuild.rawBuild.getLog(10).join('\\n')}`
                """.stripIndent().trim()
                sendTelegram(msg)
            }
        }
    }
}

def sendTelegram(String message) {
    sh """
        curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage \
            -d chat_id=${TELEGRAM_CHAT_ID} \
            --data-urlencode text="${message}" \
            -d parse_mode=Markdown
    """
}
