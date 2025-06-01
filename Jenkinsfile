pipeline {
    agent any

    environment {
        REMOTE_HOST = "192.168.100.60"
        REMOTE_USER = "root"
        REMOTE_DIR = "/var/www/capstone-project"
        BACKUP_DIR = "/var/www/capstone-project.bak"
        PHP_BIN = "/usr/bin/php82"
        COMPOSER_BIN = "/usr/local/bin/composer"
        SSH_KEY = "/var/lib/jenkins/.ssh/alpine_git"
    }

    stages {
        stage('Backup Before Deploy') {
            steps {
                echo '🗂️ Creating backup before deploy...'
                sh """
                ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
                    if [ -d "\$REMOTE_DIR" ]; then
                        rm -rf \$BACKUP_DIR
                        cp -r \$REMOTE_DIR \$BACKUP_DIR
                    fi
                '
                """
            }
        }

               stage('Git Pull on Alpine') {
            steps {
                echo '🔄 Git pull on Alpine server...'
                sh """
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} '
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
                    ssh -i ${SSH_KEY} -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} '
                        cd ${REMOTE_DIR} && ${PHP_BIN} ${COMPOSER_BIN} install --no-interaction --prefer-dist --optimize-autoloader
                    '
                """
            }
        }


        stage('Restart PHP-FPM') {
            steps {
                echo '♻️ Restarting PHP-FPM...'
                sh """
                ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
                    pkill php-fpm82 || true
                    /usr/sbin/php-fpm82 -D
                '
                """
            }
        }

        stage('Approval for Database Migration') {
            steps {
                script {
                    def userChoice = input message: 'Choose what to do with database migration:',
                        parameters: [choice(name: 'Action', choices: ['Approve - Run Migration', 'Skip Migration', 'Abort'], description: 'Migration decision')]

                    if (userChoice == 'Abort') {
                        error("❌ Deployment aborted by user.")
                    } else if (userChoice == 'Skip Migration') {
                        echo "🚫 Migration skipped as requested."
                        currentBuild.description = "Skipped migration"
                        // Set a flag so migration stage can be skipped
                        env.SKIP_MIGRATION = "true"
                    } else {
                        echo "✅ Proceeding to run migration..."
                        env.SKIP_MIGRATION = "false"
                    }
                }
            }
        }

        stage('Migrate Database on Alpine') {
            when {
                expression { env.SKIP_MIGRATION != "true" }
            }
            steps {
                echo '🛠️ Running php artisan migrate...'
                sh """
                ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
                    cd \$REMOTE_DIR && \$PHP_BIN artisan migrate --force
                '
                """
            }
        }
    }

    post {
        failure {
            echo '🔁 Rollback due to failed deployment...'
            sh """
            ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
                if [ -d "\$BACKUP_DIR" ]; then
                    rm -rf \$REMOTE_DIR
                    mv \$BACKUP_DIR \$REMOTE_DIR
                    /usr/sbin/php-fpm82 -D
                fi
            '
            """
        }

        success {
            echo '🧹 Cleaning up backup after successful deployment.'
            sh """
            ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
                rm -rf \$BACKUP_DIR
            '
            """
        }
    }
}
