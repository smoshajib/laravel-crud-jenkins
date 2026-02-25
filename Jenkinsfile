pipeline {
    agent {
        docker {
            image 'php:8.3-cli'          // PHP 8.3 CLI ইমেজ
            args '-v /tmp:/tmp'           // প্রয়োজনীয় ভলিউম
        }
    }

    environment {
        NF_TOKEN = credentials('jenkins-api')
        PROJECT_ID = 'laravel-jenkins-project'
        SERVICE_ID = 'laravel-jenkins-service'
    }

    stages {
        stage('Prepare Environment') {
            steps {
                sh '''
                    echo "📦 Installing curl, unzip, git and Composer..."
                    apt-get update && apt-get install -y curl unzip git
                    # Composer ইন্সটল
                    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
                    php composer-setup.php --quiet
                    php -r "unlink('composer-setup.php');"
                    mv composer.phar /usr/local/bin/composer
                '''
            }
        }

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                    echo "📦 Installing PHP dependencies..."
                    composer install --no-interaction --prefer-dist --optimize-autoloader
                '''
            }
        }

        stage('SAST (PHPStan)') {
            steps {
                sh '''
                    echo "🔍 Running PHPStan static analysis..."
                    vendor/bin/phpstan analyse --error-format=table
                '''
            }
        }

        stage('Deploy to Northflank') {
            steps {
                script {
                    sh """
                        echo "🚀 Triggering Northflank deployment..."
                        curl -X POST "https://api.northflank.com/v1/projects/$PROJECT_ID/services/$SERVICE_ID/deployment" \
                            -H "Authorization: Bearer $NF_TOKEN" \
                            -H "Content-Type: application/json" \
                            -d '{"branch":"main"}'
                    """
                }
            }
        }
    }

    post {
        success {
            echo '✅ Deployment triggered!'
        }
        failure {
            echo '❌ Deployment failed!'
        }
    }
}