pipeline {
    agent any

    environment {
        NORTHFLANK_TOKEN = credentials('github_actions')
        PROJECT_ID = 'laravel-crud-jenkins'        // তোমার Northflank প্রজেক্ট আইডি
        SERVICE_ID = 'laravel-crud-jenkins' // তোমার Northflank সার্ভিস আইডি
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Deploy to Northflank') {
            steps {
                script {
                    sh """
                        curl -X POST "https://api.northflank.com/v1/projects/$PROJECT_ID/services/$SERVICE_ID/deployments" \
                            -H "Authorization: Bearer $NORTHFLANK_TOKEN" \
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
