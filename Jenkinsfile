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

        stage('Backup Sebelum Deploy') {
            steps {
            echo '🗂️ Membuat backup sebelum deploy...'
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


    post {
    failure {
        echo '🔁 Rollback karena deploy gagal...'
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
        echo '🧹 Menghapus backup karena deploy berhasil.'
        sh """
        ssh -i \$SSH_KEY -o StrictHostKeyChecking=no \$REMOTE_USER@\$REMOTE_HOST '
            rm -rf \$BACKUP_DIR
        '
        """
    }
}

}
