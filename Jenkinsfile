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
                    sendTelegram("📥 Checkout complete.")
                }
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
                script {
                    sendTelegram("📦 Backup created before deployment.")
                }
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
                script {
                    sendTelegram("📥 Git pull completed on Alpine server.")
                }
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
                script {
                    sendTelegram("📦 Composer dependencies installed.")
                }
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
                script {
                    sendTelegram("♻️ PHP-FPM restarted.")
                }
            }
        }

        stage('Approval for Database Migration') {
            steps {
                script {
                    def userInput = input message: '🚨 Proceed with database migration?', parameters: [
                        choice(name: 'Decision', choices: ['Approve - Run Migrate', 'Reject - Skip Migration', 'Abort Deployment'], description: 'Select your action')
                    ]

                    if (userInput == 'Abort Deployment') {
                        sendTelegram("❌ Deployment aborted by user.")
                        error("Deployment aborted by user.")
                    } else if (userInput == 'Reject - Skip Migration') {
                        currentBuild.description = 'Migration skipped'
                        sendTelegram("⏭️ Migration step skipped by user.")
                    } else {
                        currentBuild.description = 'Running migration'
                        env.DO_MIGRATION = "true"
                        sendTelegram("🔧 Approved: Proceeding with migration.")
                    }
                }
            }
        }

        stage('Migrate Database on Alpine') {
            when {
                expression { return env.DO_MIGRATION == "true" }
            }
            steps {
                echo '🛠️ Running php artisan migrate...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ./artisan migrate --force
                    '
                """
                script {
                    sendTelegram("🛠️ Database migration completed.")
                }
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
                script {
                    sendTelegram("🧹 Permissions fixed and cache cleared.")
                }
            }
        }
    }

    post {
        success {
            script {
                sendTelegram("✅ Deployment completed successfully.")
            }
        }
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
            script {
                sendTelegram("❌ Deployment failed. Rollback executed.")
            }
        }
    }
}

// Custom function to send Telegram notification
def sendTelegram(message) {
    sh """
        curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage \
        -d chat_id=${TELEGRAM_CHAT_ID} \
        -d text='[Capstone CI/CD] ${message}'
    """
}
