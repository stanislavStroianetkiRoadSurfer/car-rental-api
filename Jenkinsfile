pipeline {
    agent any

    environment {
        REPOSITORY_NAME = ""
        ECR_REGISTRY = "your.registry"
        IMAGE_NAME = "${ECR_REGISTRY}/${REPOSITORY_NAME}"
    }

    stages {
        stage("Build Test Image") {
            steps {
                sh '''
                    docker build -t ${IMAGE_NAME}:test -f docker/php/Dockerfile .
                '''
            }
        }

        stage('Install dev dependencies') {
            steps {
                script {
                    docker.image("$IMAGE_NAME:test").inside() {
                        sh 'composer install'
                    }
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    docker.image("$IMAGE_NAME:test").inside() {
                        sh 'php ./bin/phpunit'
                    }
                }
            }
        }
    }

    post {
        always {
            cleanWs()
        }
    }
}
