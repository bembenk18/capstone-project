pipeline {
    agent any

    environment {
        REMOTE_DIR = "/srv/www/project"
        BACKUP_DIR = "/srv/www/project_backup"
        GIT_URL = "https://github.com/bembenk18/capstone-project.git"
        TELEGRAM_API = "https://api.telegram.org/bot${TELEGRAM_TOKEN}"
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    def commitMessage = sh(script: "git log -1 --pretty=%s", returnStdout: true).trim()
                    def authorEmail = sh(script: "git log -1 --pretty=format:%ae", returnStdout: true).trim()
                    currentBuild.description = "Deploy by ${authorEmail}"

                    def message = "🚀 Deploy by ${authorEmail}\n📝 ${commitMessage}\n\n🏁 Stage: Checkout"
                    writeFile file: 'telegram.txt', text: message
                    def response = sh(script: "curl -s -X POST ${env.TELEGRAM_API}/sendMessage -d chat_id=${TELEGRAM_CHAT_ID} --data-urlencode text@telegram.txt -d parse_mode=Markdown", returnStdout: true).trim()
                    def messageId = (response =~ /"message_id":(\d+)/)[0][1]
                    writeFile file: 'message_id.txt', text: messageId
                }
            }
        }

        stage('Backup Before Deploy') {
            steps {
                script {
                    def messageId = readFile('message_id.txt').trim()
                    def text = readFile('telegram.txt').trim() + "\n\n🔄 Stage: Backup Before Deploy"
                    sh """
                        curl -s -X POST ${env.TELEGRAM_API}/editMessageText \
                        -d chat_id=${TELEGRAM_CHAT_ID} \
                        -d message_id=${messageId} \
                        --data-urlencode text="${text}" \
                        -d parse_mode=Markdown
                    """

                    sh """
                        ssh -i /var/lib/jenkins/.ssh/alpine_git -o StrictHostKeyChecking=no root@192.168.100.60 '
                        if [ -d "${REMOTE_DIR}" ]; then
                            rm -rf "${BACKUP_DIR}"
                            cp -r "${REMOTE_DIR}" "${BACKUP_DIR}"
                        fi'
                    """
                }
            }
        }

        stage('Git Pull on Alpine') {
            steps {
                script {
                    def messageId = readFile('message_id.txt').trim()
                    def text = readFile('telegram.txt').trim() + "\n\n🔄 Stage: Git Pull on Alpine"
                    sh """
                        curl -s -X POST ${env.TELEGRAM_API}/editMessageText \
                        -d chat_id=${TELEGRAM_CHAT_ID} \
                        -d message_id=${messageId} \
                        --data-urlencode text="${text}" \
                        -d parse_mode=Markdown
                    """

                    sh """
                        ssh -i /var/lib/jenkins/.ssh/alpine_git -o StrictHostKeyChecking=no root@192.168.100.60 '
                        set -e
                        if [ -d "${REMOTE_DIR}/.git" ]; then
                            echo "[INFO] Repo git valid, pull update"
                            cd ${REMOTE_DIR}
                            git reset --hard HEAD
                            git pull origin main
                        else
                            echo "[ERROR] Repo git tidak valid, rollback"
                            exit 1
                        fi'
                    """
                }
            }
        }

        stage('Install Dependencies on Alpine') {
            steps {
                echo "Install dependencies..."
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                echo "Restart PHP-FPM..."
            }
        }
    }

    post {
        success {
            script {
                def messageId = readFile('message_id.txt').trim()
                def text = readFile('telegram.txt').trim() + "\n\n✅ Deployment berhasil!"
                sh """
                    curl -s -X POST ${env.TELEGRAM_API}/editMessageText \
                    -d chat_id=${TELEGRAM_CHAT_ID} \
                    -d message_id=${messageId} \
                    --data-urlencode text="${text}" \
                    -d parse_mode=Markdown
                """
            }
        }

        failure {
            script {
                def messageId = readFile('message_id.txt').trim()
                def text = readFile('telegram.txt').trim() + "\n\n❌ Deployment gagal, rollback..."
                sh """
                    curl -s -X POST ${env.TELEGRAM_API}/editMessageText \
                    -d chat_id=${TELEGRAM_CHAT_ID} \
                    -d message_id=${messageId} \
                    --data-urlencode text="${text}" \
                    -d parse_mode=Markdown
                """
                sh """
                    ssh -i /var/lib/jenkins/.ssh/alpine_git -o StrictHostKeyChecking=no root@192.168.100.60 '
                    if [ -d "${BACKUP_DIR}" ]; then
                        rm -rf "${REMOTE_DIR}"
                        mv "${BACKUP_DIR}" "${REMOTE_DIR}"
                        /usr/sbin/php-fpm82 -D
                    fi'
                """
            }
        }
    }
}
