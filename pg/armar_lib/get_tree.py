import os
from connexion import *
import csv
import string

ses_dict={}
SesUprList=[]
dep=[]

conn=conectarse('yvasa850',os.environ['User'],os.environ['Pass'])
conn.make_sudo('mwpsyz01','Sam0la\'P','mwpsyz01 >')

i=0
for ses,upr,nom in csv.reader(open("ses_upr.csv")):
	if upr.find('U09') != -1:
    		ses_dict[ses]=i
		SesUprList.append([ses,upr])
    		i=i+1

#print ses_dict
i#print SesUprList

w = csv.writer(open("tree.csv", "w"))

for ses,upr in SesUprList:
	buff=conn.get_tree(upr)	
	flag=0
	for sal in buff.split("\n"):
	#	flag=0
	        if sal.find(">") == -1 and sal.find("uxshw") == -1 :
			salida=sal.split(":")
			if salida[1] not in dep:
	        	        dep.append(salida[1])
			#	print salida[1].translate(string.maketrans("\n\t\r ", "    ")).strip()
				for u in csv.reader(open("ses_upr.csv")):
			#		print u[1]
			#		print salida[1].strip()
                			if  salida[1].strip() in u[1] :
						SES=u[0]
						print SES
						flag=1
						logging.info("Encontre una dependencia")
						w.writerow([ses_dict[ses],ses_dict[SES],ses])
	if flag == 0:
		 w.writerow([ses_dict[ses],-1,ses])
						
#for u in upr:
#	print u
#ssh.close()
print dep
conn.close()
