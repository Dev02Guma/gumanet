pipeline {
    agent any
    environment {
		DOCKERHUB_CREDENTIALS=credentials('DockerHub')
        APP_VERSION = 'v1.0'
	}
    stages {
        stage('Docker Build') {
            steps {
                sh "docker build -t gumadesarrollo/gumanet:${APP_VERSION}.${env.BUILD_NUMBER} ."
            }
        }
        stage('Docker Push') {
            steps {
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh "docker push gumadesarrollo/gumanet:${APP_VERSION}.${env.BUILD_NUMBER}"
            }
        }
        stage('Docker Remove Image') {
            steps {
                sh "docker rmi gumadesarrollo/gumanet:${APP_VERSION}.${env.BUILD_NUMBER}"
            }
        }
        stage('Apply Kubernetes'){
            steps {
                sh 'cat Deployment.yaml | sed "s/${APP_VERSION}.{{BUILD_NUMBER}}/${APP_VERSION}.$BUILD_NUMBER/g" | kubectl apply -f -'
                sh 'kubectl apply -f service.yaml'            
            }
        }
    }
}