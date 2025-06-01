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

    options {
        skipStagesAfterUnstable()
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                script {
                    def commitMsg = sh(script: 'git log -1 --pretty=%s', returnStdout: true).trim()
                    def author = sh(script: 'git log -1 --pretty=format:%ae', returnStdout: true).trim()
                    env.DEPLOY_SUMMARY = "🚀 Deploy by ${author}\n📝 ${commitMsg}"
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n🏁 Stage: Checkout")
                }
            }
        }

        stage('Backup Before Deploy') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n🔄 Stage: Backup Before Deploy")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        if [ -d "${REMOTE_DIR}" ]; then
                            rm -rf ${BACKUP_DIR}
                            cp -r ${REMOTE_DIR} ${BACKUP_DIR}
                        fi
                    '
                '''
            }
        }

        stage('Git Pull on Alpine') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n🔄 Stage: Git Pull on Alpine")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        git config --global --add safe.directory ${REMOTE_DIR}

                        if [ ! -d "${REMOTE_DIR}/.git" ]; then
                            echo "[WARN] Folder bukan repo git, hapus dan clone ulang"
                            rm -rf ${REMOTE_DIR}
                            git clone https://github.com/bembenk18/capstone-project.git ${REMOTE_DIR}
                        else
                            echo "[INFO] Pull latest update"
                            cd ${REMOTE_DIR}
                            git reset --hard
                            git pull origin main
                        fi
                    '
                '''
            }
        }

        stage('Install Dependencies on Alpine') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n📦 Stage: Install Dependencies")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ${COMPOSER_BIN} install --no-interaction --prefer-dist --optimize-autoloader
                    '
                '''
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n♻️ Stage: Restart PHP-FPM")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        pkill php-fpm82 || true
                        /usr/sbin/php-fpm82 -D
                    '
                '''
            }
        }

        stage('Approval for Database Migration') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n⏳ Stage: Waiting for DB Migration Approval")
                    def userInput = input message: '🚨 Lanjutkan migrasi database?', parameters: [choice(name: 'Action', choices: ['Yes', 'Skip', 'Abort'], description: 'Pilih aksi:')]
                    if (userInput == 'Abort') {
                        error("❌ Deployment aborted by user.")
                    } else if (userInput == 'Skip') {
                        currentBuild.description = 'Migration Skipped'
                        env.SKIP_MIGRATE = 'true'
                    }
                }
            }
        }

        stage('Migrate Database on Alpine') {
            when {
                expression { return env.SKIP_MIGRATE != 'true' }
            }
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n🛠️ Stage: Database Migration")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} artisan migrate --force
                    '
                '''
            }
        }

        stage('Fix Permissions & Clear Cache') {
            steps {
                script {
                    sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n🔧 Stage: Fix Permissions & Clear Cache")
                }
                sh '''
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR}
                        chmod -R guo+w storage bootstrap/cache
                        ${PHP_BIN} artisan cache:clear
                        ${PHP_BIN} artisan config:clear
                        ${PHP_BIN} artisan view:clear
                        ${PHP_BIN} artisan route:clear
                    '
                '''
            }
        }
    }

    post {
        success {
            script {
                sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n✅ Deployment berhasil.")
            }
        }
        failure {
            script {
                sendOrEditTelegram("${env.DEPLOY_SUMMARY}\n\n❌ Deployment gagal, rollback...")
            }
            sh '''
                ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                    if [ -d "${BACKUP_DIR}" ]; then
                        rm -rf ${REMOTE_DIR}
                        mv ${BACKUP_DIR} ${REMOTE_DIR}
                        /usr/sbin/php-fpm82 -D
                    fi
                '
            '''
        }
    }
}

def sendOrEditTelegram(String message) {
    def file = '/tmp/telegram_message_id.txt'
    def msgId = ''
    if (fileExists(file)) {
        msgId = readFile(file).trim()
    }

    def response
    if (msgId) {
        response = sh(
            script: """
                curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/editMessageText \
                    -d chat_id=${TELEGRAM_CHAT_ID} \
                    -d message_id=${msgId} \
                    --data-urlencode text='${message}' \
                    -d parse_mode=Markdown
            """,
            returnStdout: true
        ).trim()
    }

    if (!msgId || !response.contains('message_id')) {
        response = sh(
            script: """
                curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage \
                    -d chat_id=${TELEGRAM_CHAT_ID} \
                    --data-urlencode text='${message}' \
                    -d parse_mode=Markdown
            """,
            returnStdout: true
        ).trim()
        def newId = sh(script: "echo '${response}' | grep -o '\"message_id\":[0-9]*' | cut -d ':' -f2", returnStdout: true).trim()
        if (newId) {
            writeFile file: file, text: newId
        }
    }
}
