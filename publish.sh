#ssh -i /Users/theus/ganir.pem ec2-user@ec2-54-93-53-225.eu-central-1.compute.amazonaws.com
rsync  -rlth  -e '/usr/bin/ssh -i /Users/theus/ganir.pem' -v --force --omit-dir-times --exclude-from=./excluded.list ".//" "ec2-user@ec2-54-93-53-225.eu-central-1.compute.amazonaws.com:/opt/sites/global//"
