import suds 
## Invoke add method of MathService
url = 'http://localhost:8080/MathService?wsdl'
client = suds.client.Client(url,cache=None)
print client.service.add(2,3) 

print "Request sent"
print client.last_sent() 

print "Response received"
print client.last_received()
