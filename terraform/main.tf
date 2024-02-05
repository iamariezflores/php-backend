provider "aws" {
  region = "us-east-1"
}

resource "aws_ecs_cluster" "mailer_cluster" {
  name = "mailer_cluster"
}

resource "aws_ec5_task_defination" "mailer_frontend" {
  family                    = "frontend-task"
  network_mode              = "awsvpc"
  requires_compatibilities  = ["FARGATE"]
  cpu                       = "256"
  memory                    = "512"

  container_definitions = jsonencode([
    {
        name = "frontend-container" 
        image = "mailer-frontend-image:tag" #assumes that this is the name of the image
        portMappings = [
            {
                containerPort = 80,
                hostPort = 80,
            }
        ]
    }
  ])
}

resource "aws_ec5_service" "mailer_frontend_service" {
  name            = "frontend-service"
  cluster         = aws_ecs_cluster.my_ecs_cluster.id
  task_definition = aws_ecs_task_definition.frontend_task_definition.arn
  launch_type     = "FARGATE"
  desired_count   = 2  
  network_configuration {
    subnets = ["subnet-xxxxxxxxxxxxxxxxx"]      # Specify valid subnet IDs
    security_groups = ["sg-xxxxxxxxxxxxxxxxx"]  # Specify valid security group IDs
  }
}

resource "aws_ecs_task_definition" "backend_task_definition" {
  family                   = "backend-task"
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = "256"  
  memory                   = "512"  

  container_definitions = jsonencode([
    {
      name  = "backend-container",
      image = "mailer-backend-image:tag",  #assumes that this is the name of the image
      portMappings = [
        {
          containerPort = 8080,
          hostPort      = 8080,
        },
      ],
    },
  ])
}

resource "aws_ecs_service" "backend_service" {
  name            = "backend-service"
  cluster         = aws_ecs_cluster.my_ecs_cluster.id
  task_definition = aws_ecs_task_definition.backend_task_definition.arn
  launch_type     = "FARGATE"
  desired_count   = 2  
  network_configuration {
    subnets = ["subnet-xxxxxxxxxxxxxxxxx"]  # Specify valid subnet IDs
    security_groups = ["sg-xxxxxxxxxxxxxxxxx"]  # Specify valid security group IDs
  }
}
