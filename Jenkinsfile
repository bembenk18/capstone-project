pipeline {
    agent any

    environment {
        REMOTE_HOST = "192.168.100.60"
        REMOTE_USER = "root"
        REMOTE_DIR = "/var/www/capstone-project"
        PHP_BIN = "/usr/bin/php82"
        COMPOSER_BIN = "/usr/local/bin/composer"
        SSH_KEY = "/var/lib/jenkins/.ssh/alpine_git" // path SSH key Jenkins
    }

    stages {
        stage('Git Pull on Alpine') {
            steps {
                echo '🔄 Git pull di server Alpine...'
                sh """
                ssh -i $SSH_KEY -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST '
                    git config --global --add safe.directory $REMOTE_DIR

                    if [ ! -d "$REMOTE_DIR" ]; then
                        git clone https://github.com/bembenk18/capstone-project.git $REMOTE_DIR;
                    else
                        cd $REMOTE_DIR && git pull origin main;
                    fi
                '
                """
            }
        }

        stage('Install Dependencies on Alpine') {
            steps {
                echo '📦 Menjalankan composer install...'
                sh """
                ssh -i $SSH_KEY -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST '
                    cd $REMOTE_DIR && $COMPOSER_BIN install --no-interaction --prefer-dist --optimize-autoloader
                '
                """
            }
        }

        

        stage('Restart PHP-FPM') {
            steps {
                echo '♻️ Restart PHP-FPM...'
                sh """
                ssh -i $SSH_KEY -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST '
                    pkill php-fpm82 || true
                    /usr/sbin/php-fpm82 -D
                '
                """
            }
        }
        stage('Fix Permissions & Clear Cache') {
    steps {
        echo '🧹 Memperbaiki permission dan menghapus cache Laravel...'
        sh """
        ssh -i $SSH_KEY -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST '
            cd $REMOTE_DIR

            chmod -R g+rw storage
            chown -R nginx:nginx storage bootstrap/cache
            chmod -R 775 bootstrap/cache

            $PHP_BIN artisan cache:clear
        '
        """
    }

    stage('Manual Approval to Migrate') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    input message: 'Lanjutkan ke database migration (php artisan migrate --force)?'
                }
            }
        }

        stage('Migrate Database on Alpine') {
            steps {
                echo '🛠️ Menjalankan php artisan migrate...'
                sh """
                ssh -i $SSH_KEY -o StrictHostKeyChecking=no $REMOTE_USER@$REMOTE_HOST '
                    cd $REMOTE_DIR && $PHP_BIN artisan migrate
                '
                """
            }
        }
}
    }


    post {
        success {
            echo '✅ Deploy Laravel ke Alpine sukses!'
        }
        failure {
            echo '❌ Deploy gagal. Cek log build.'
        }
    }
}
