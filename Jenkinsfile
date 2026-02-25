pipeline {
    // এজেন্ট হিসেবে যে কোনো নোড (Jenkins কন্টেইনার নিজেই)
    agent any

    environment {
        // Jenkins-এ সংরক্ষিত Northflank API টোকেন
        NF_TOKEN = credentials('jenkins-api')
        // Northflank প্রজেক্ট ও সার্ভিস আইডি (তোমার দেওয়া)
        PROJECT_ID = 'laravel-jenkins-project'
        SERVICE_ID = 'laravel-jenkins-service'
    }

    stages {
        // স্টেজ ১: ডকার ইমেজ প্রস্তুত করা (প্রথমবার টেনে আনবে, পরে ক্যাশ থেকে নেবে)
        stage('Prepare Docker Image') {
            steps {
                sh 'docker pull php:8.3-cli'   
            }
        }

        // স্টেজ ২: Composer নির্ভরতা ইন্সটল
        stage('Install Dependencies') {
            steps {
                sh '''
                    echo "📦 Installing Composer and PHP dependencies..."
                    docker run --rm -v $PWD:/app -w /app php:8.3-cli bash -c "
                        apt-get update &&
                        apt-get install -y curl unzip git &&
                        php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\" &&
                        php composer-setup.php --quiet &&
                        php -r \"unlink('composer-setup.php');\" &&
                        mv composer.phar /usr/local/bin/composer &&
                        composer install --no-interaction --prefer-dist --optimize-autoloader
                    "
                '''
            }
        }

        // স্টেজ ৩: PHPStan স্ট্যাটিক অ্যানালাইসিস
        stage('SAST (PHPStan)') {
            steps {
                sh '''
                    echo "🔍 Running PHPStan static analysis..."
                    docker run --rm -v $PWD:/app -w /app php:8.3-cli vendor/bin/phpstan analyse --error-format=table
                '''
            }
        }

        // স্টেজ ৪: Northflank-এ ডিপ্লয় ট্রিগার
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

    // পোস্ট অ্যাকশন – ফলাফল জানানো
    post {
        success {
            echo '✅ Deployment triggered successfully!'
        }
        failure {
            echo '❌ Pipeline failed. Check console output for details.'
        }
    }
}