pipeline {
    agent { label 'ht1' }

    stages {
        stage('Build') {
            steps {

                // Print all the environment variables.
                sh 'printenv'
                sh 'echo $GIT_BRANCH'
                sh 'echo $GIT_COMMIT'
                echo 'Construction des images'
                sh 'cd web/docker && docker-compose build'
                echo 'Lancement du docker-compose'
                sh 'docker-compose -f web/docker/docker-compose.yml up'
            }
        }
        stage('Test') {
            steps {
                echo 'PHP Unit tests'
                sh 'web/tests/phpunit_docker.sh'
            }
        }

    }
    post {
        always {
            // Always cleanup after the build.
            sh 'docker-compose -f web/docker/docker-compose.yml down'
        }
    }
}