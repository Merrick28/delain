pipeline {
    agent none

    stages {
        stage('Build') {
            agent { label 'ht1' }
            when {
                not {
                changelog '.*^\\[ci skip\\] .+$'
                }
            }
            steps {

                // Print all the environment variables.
                sh 'printenv'
                sh 'echo $GIT_BRANCH'
                sh 'echo $GIT_COMMIT'
                echo 'Construction des images'
                sh 'docker pull delain/tests_unitaires'
                sh 'docker-compose -f docker-compose-tu.yml build'
                echo 'Arrêt des instances précédentes '
                sh 'docker-compose -f docker-compose-tu.yml down --remove-orphans'
                sh 'docker rm -f webtu delain_dbtu'
                echo 'Lancement du docker-compose'
                sh 'docker-compose -f docker-compose-tu.yml up -d'
            }
        }
        stage('Test') {
            agent { label 'ht1' }
            when {
                not {
                changelog '.*^\\[ci skip\\] .+$'
                }
            }
            steps {

                echo "Wait for postgres to be up"
                sh 'docker exec webtu /home/delain/delain/web/tests/wait.sh'
                echo 'PHP Unit tests'
                sh 'web/tests/phpunit_docker-tu.sh'
            }

        }
        stage('Deploy')
        {
            agent { label 'backenddelain' }
            when { branch 'master' }
            steps {
                echo "Git pull"
                sh "cd /home/delain/delain && git pull"
                echo "Rights to delain"
                sh "cd /home/delain && chown -R delain: delain"
                echo "Empty cache"
                sh "rm -rf /home/delain/delain/cache/*"
                echo "Livraisons SQL"
                sh "su - delain  /home/delain/delain/shell/livraisons.sh"

            }
        }


    }
    post {

        always {
            agent { label 'ht1' }
            // Always cleanup after the build.
            sh 'docker-compose -f docker-compose-tu.yml down'
        }
    }
}