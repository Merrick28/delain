pipeline {
    agent { label 'ht1' }

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
                sh "su - delain -c 'cd /home/delain/delain && git pull'"
                echo "Empty cache"
                sh "rm -rf /home/delain/delain/cache/*"
                echo "Livraisons SQL"
                sh "su - delain  /home/delain/delain/shell/livraisons.sh"

            }
        }


    }
    post {

        always {
            // Always cleanup after the build.
            sh 'docker-compose -f docker-compose-tu.yml down'
        }
        failure {
                     mail to: 'stephane.dewitte@gmail.com',
                                  subject: "Failed Pipeline: ${currentBuild.fullDisplayName}",
                                  body: "<b>Error on project</b><br>Project: ${env.JOB_NAME} <br>Build Number: ${env.BUILD_NUMBER} <br> URL de build: ${env.BUILD_URL}",
                                  charset: 'UTF-8',
                                  mimeType: 'text/html',
                                  from: 'stephane@sdewitte.net'
                     slackSend channel: '#general',
                                       color: 'red',
                                       message: "The pipeline ${currentBuild.fullDisplayName} is down."
                 }
         changed {
          mail to: 'stephane.dewitte@gmail.com',
                                           subject: "Unstable Pipeline: ${currentBuild.fullDisplayName}",
                                           body: "<b>Unstable</b><br>Project: ${env.JOB_NAME} <br>Build Number: ${env.BUILD_NUMBER} <br> URL de build: ${env.BUILD_URL}",
                                           charset: 'UTF-8',
                                           mimeType: 'text/html',
                                           from: 'stephane@sdewitte.net'
       slackSend channel: '#general',
                                              color: 'orange',
                                              message: "The pipeline ${currentBuild.fullDisplayName} is unstable."
         }
    }
}