pipeline {
    agent any

    environment {
        REMOTE_HOST = "root@192.168.100.60"
        REMOTE_DIR = "/var/www/capstone-project"
        BACKUP_DIR = "/var/www/backup-capstone"
        PHP_BIN = "/usr/bin/php82"
        COMPOSER_BIN = "/usr/local/bin/composer"
        SSH_KEY = "/var/lib/jenkins/.ssh/alpine_git"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Backup Before Deploy') {
            steps {
                echo '🗂️ Creating backup before deploy...'
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
                echo '🔄 Git pull on Alpine server...'
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

        stage('Install Dependencies on Alpine') {
            steps {
                echo '📦 Running composer install...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ${COMPOSER_BIN} install --no-interaction --prefer-dist --optimize-autoloader
                    '
                """
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                echo '♻️ Restarting PHP-FPM...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        pkill php-fpm82 || true
                        /usr/sbin/php-fpm82 -D
                    '
                """
            }
        }

        stage('Approval for Database Migration') {
            steps {
                script {
                    input message: '🚨 Lanjutkan migrasi database?', ok: 'Yes, Run Migrate'
                    echo '✅ Proceeding to run migration...'
                }
            }
        }

        stage('Migrate Database on Alpine') {
            steps {
                echo '🛠️ Running php artisan migrate...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ./artisan migrate --force
                    '
                """
            }
        }

        stage('Fix Permissions & Clear Cache') {
            steps {
                echo '🔧 Fixing permissions and clearing cache...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR}
                        chmod -R guo+w storage bootstrap/cache
                        ${PHP_BIN} ./artisan cache:clear
                        ${PHP_BIN} ./artisan config:clear
                        ${PHP_BIN} ./artisan view:clear
                        ${PHP_BIN} ./artisan route:clear
                    '
                """
            }
        }
    }

    post {
        failure {
            echo '🔁 Rollback due to failed deployment...'
            sh """
                ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                    if [ -d "${BACKUP_DIR}" ]; then
                        rm -rf ${REMOTE_DIR}
                        mv ${BACKUP_DIR} ${REMOTE_DIR}
                        /usr/sbin/php-fpm82 -D
                    fi
                '
            """
        }
    }
}
