pipeline {
    agent any 

    environment {
        // Jenkins-এ সংরক্ষিত credential-এর নাম (যেখানে Northflank API টোকেন আছে)
        NF_TOKEN = credentials('jenkins-api')
        
        // Northflank প্রজেক্ট ও সার্ভিস আইডি
        PROJECT_ID = 'laravel-jenkins-project'
        SERVICE_ID = 'laravel-jenkins-service'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        // নতুন স্টেজ: SAST – PHPStan দিয়ে স্ট্যাটিক অ্যানালাইসিস
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