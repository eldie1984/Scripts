import paramiko

HOST=''
USER=''
PASS=''
REMOTE_USER=''
REMOTE_PASS=''
END_SENTENCE=''

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(HOST, username=USER, password=PASS)

chan = ssh.invoke_shell()

# Ssh and wait for the password prompt.
chan.send('su -  REMOTE_USER \n')
buff = ''
i=0
while not 'Password:' in buff:
    resp = chan.recv(9999)
    buff += resp
    i = i + 1
    print "%s: %s" %(i,resp)
    if i==20 or resp.endswith('Password:'):
	ssh.close()
	exit()

# Send the password and wait for a prompt.
chan.send('REMOTE_PASS\n')
buff = ''
i=0
while not END_SENTENCE in buff:
    resp = chan.recv(9999)
    buff += resp
    i = i + 1
    print "%s: %s" %(i,resp)
    if i==20 or resp.endswith('mwpsyz01 >'):
        ssh.close()
        exit()
#
## Execute whatever command and wait for a prompt again.
chan.send('ls\n')
buff = ''
while not END_SENTENCE in buff:
    resp = chan.recv(9999)
    buff += resp

# Now buff has the data I need.
print 'buff', buff

ssh.close()

