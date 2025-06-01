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

                script {
                    COMMIT_MSG = sh(script: "git log -1 --pretty=%s", returnStdout: true).trim()
                    COMMIT_EMAIL = sh(script: "git log -1 --pretty=format:%ae", returnStdout: true).trim()
                    currentBuild.description = "📝 ${COMMIT_MSG} by ${COMMIT_EMAIL}"
                }

                withCredentials([
                    string(credentialsId: 'TELEGRAM_TOKEN', variable: 'TELEGRAM_TOKEN'),
                    string(credentialsId: 'TELEGRAM_CHAT_ID', variable: 'TELEGRAM_CHAT_ID'),
                    string(credentialsId: 'TELEGRAM_MESSAGE_ID', variable: 'TELEGRAM_MESSAGE_ID')
                ]) {
                    sh """
                        curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/editMessageText \
                            -d chat_id=${TELEGRAM_CHAT_ID} \
                            -d message_id=${TELEGRAM_MESSAGE_ID} \
                            --data-urlencode text="🚀 Deploy by ${COMMIT_EMAIL}
📝 ${COMMIT_MSG}

🏁 Stage: Checkout" \
                            -d parse_mode=Markdown
                    """
                }
            }
        }

        stage('Backup Before Deploy') {
            steps {
                telegramStage('Backup Before Deploy')
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
                telegramStage('Git Pull on Alpine')
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        git config --global --add safe.directory ${REMOTE_DIR}

                        if [ -d "${REMOTE_DIR}/.git" ]; then
                            cd ${REMOTE_DIR} && git reset --hard HEAD && git pull origin main
                        else
                            echo "[WARN] Folder bukan repo git dan tidak bisa pull. Gagal!"
                            exit 1
                        fi
                    '
                """
            }
        }

        stage('Install Dependencies on Alpine') {
            steps {
                telegramStage('Install Dependencies on Alpine')
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ${COMPOSER_BIN} install --no-interaction --prefer-dist --optimize-autoloader
                    '
                """
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                telegramStage('Restart PHP-FPM')
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
                input message: '🚨 Lanjutkan migrasi database?', ok: 'Yes, Run Migrate'
                telegramStage('Approved: Migration')
            }
        }

        stage('Migrate Database on Alpine') {
            steps {
                telegramStage('Migrate Database on Alpine')
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} artisan migrate --force
                    '
                """
            }
        }

        stage('Fix Permissions & Clear Cache') {
            steps {
                telegramStage('Fix Permissions & Clear Cache')
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
            sendTelegram("✅ Deployment berhasil.")
        }
        failure {
            sendTelegram("❌ Deployment gagal, rollback...")

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

def telegramStage(stageName) {
    withCredentials([
        string(credentialsId: 'TELEGRAM_TOKEN', variable: 'TELEGRAM_TOKEN'),
        string(credentialsId: 'TELEGRAM_CHAT_ID', variable: 'TELEGRAM_CHAT_ID'),
        string(credentialsId: 'TELEGRAM_MESSAGE_ID', variable: 'TELEGRAM_MESSAGE_ID')
    ]) {
        sh """
            curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/editMessageText \
                -d chat_id=${TELEGRAM_CHAT_ID} \
                -d message_id=${TELEGRAM_MESSAGE_ID} \
                --data-urlencode text="🚀 Deploy by ${COMMIT_EMAIL}
📝 ${COMMIT_MSG}

🔄 Stage: ${stageName}" \
                -d parse_mode=Markdown
        """
    }
}

def sendTelegram(msg) {
    withCredentials([
        string(credentialsId: 'TELEGRAM_TOKEN', variable: 'TELEGRAM_TOKEN'),
        string(credentialsId: 'TELEGRAM_CHAT_ID', variable: 'TELEGRAM_CHAT_ID'),
        string(credentialsId: 'TELEGRAM_MESSAGE_ID', variable: 'TELEGRAM_MESSAGE_ID')
    ]) {
        sh """
            curl -s -X POST https://api.telegram.org/bot${TELEGRAM_TOKEN}/editMessageText \
                -d chat_id=${TELEGRAM_CHAT_ID} \
                -d message_id=${TELEGRAM_MESSAGE_ID} \
                --data-urlencode text="🚀 Deploy by ${COMMIT_EMAIL}
📝 ${COMMIT_MSG}

${msg}" \
                -d parse_mode=Markdown
        """
    }
}
