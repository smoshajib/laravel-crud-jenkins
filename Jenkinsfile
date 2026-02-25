pipeline {
    agent any

    environment {
        NF_TOKEN = credentials('jenkins-api')
        PROJECT_ID = 'laravel-jenkins-project'
        SERVICE_ID = 'laravel-jenkins-service'
    }

    stages {
        stage('Prepare Docker Image') {
            steps {
                sh 'docker pull php:8.3-cli'
            }
        }

        stage('Check and Fix Script') {
            steps {
                sh '''
                    echo "🔍 Checking if install-composer.sh exists..."
                    ls -la install-composer.sh
                    echo "🔧 Fixing line endings..."
                    sed -i 's/\\r$//' install-composer.sh
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                sh """
                    echo "📦 Installing Composer and PHP dependencies..."
                    docker run --rm -v ${env.WORKSPACE}:/app -w /app php:8.3-cli bash /app/install-composer.sh
                """
            }
        }

        stage('SAST (PHPStan)') {
            steps {
                sh '''
                    echo "🔍 Running PHPStan static analysis..."
                    docker run --rm -v ${env.WORKSPACE}:/app -w /app php:8.3-cli vendor/bin/phpstan analyse --error-format=table
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
            echo '✅ Deployment triggered successfully!'
        }
        failure {
            echo '❌ Pipeline failed. Check console output for details.'
        }
    }
}