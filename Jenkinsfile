pipeline {
    agent any

    environment {
        APP_ENV = 'production'
        DEPLOY_DIR = '/var/www/capstone-project'
        PHP_BIN = '/usr/bin/php82'
    }

    stages {
        stage('Pull Latest Code') {
            steps {
                echo '🔄 Pulling latest code...'
                sh "cd $DEPLOY_DIR && git pull origin main"
            }
        }

        stage('Install Dependencies') {
            steps {
                echo '📦 Installing Composer dependencies...'
                sh "cd $DEPLOY_DIR && composer install --no-interaction --prefer-dist --optimize-autoloader"
            }
        }

        stage('Manual Approval to Migrate DB') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    input message: 'Apakah kamu ingin menjalankan `php artisan migrate --force`?'
                }
            }
        }

        stage('Migrate Database') {
            steps {
                echo '🛠️ Running Laravel migrations...'
                sh "cd $DEPLOY_DIR && $PHP_BIN artisan migrate --force"
            }
        }

        stage('Restart PHP-FPM') {
            steps {
                echo '♻️ Restarting PHP-FPM...'
                sh "pkill php-fpm82 || true"
                sh "/usr/sbin/php-fpm82 -D"
            }
        }
    }

    post {
        success {
            echo '✅ Deployment completed successfully!'
        }
        failure {
            echo '❌ Deployment failed!'
        }
    }
}
