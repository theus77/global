rsync  -rlth -v --force --exclude-from=./excluded.list ".//" "ec2-user@ec2-54-93-66-79.eu-central-1.compute.amazonaws.com:/var/www/html//"
