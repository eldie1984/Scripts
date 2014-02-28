import paramiko
ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(
    paramiko.AutoAddPolicy())

ssh.connect('yvasa850', username='e449806', 
    password='Ema84nue')
#stdin, stdout, stderr = ssh.exec_command(
#    "su - mwpsyz01")
print "Sam0la\'P\n"
#stdin.write('Sam0la\'P\n')
#stdin.flush()
#data = stdout.read().splitlines()
#for line in data:
#    print line
#data = stderr.read().splitlines()
#for line in data:
#    print line
#
#stdin, stdout, stderr = ssh.exec_command(
#    "uxlst ses ses=*")
#data = stdout.read().splitlines()
#for line in data:
#    print line
#
ssh.close()
