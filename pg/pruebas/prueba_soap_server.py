import tornado.httpserver
import tornado.ioloop
from tornadows import soaphandler
from tornadows import webservices
from tornadows import xmltypes
from tornadows.soaphandler import webservice 

class MathService(soaphandler.SoapHandler):
	@webservice(_params=[int,int],_returns=int)
	def add(self, a, b):                
		return a+b 
if __name__ == '__main__':
	service = [('MathService',MathService)]
	ws = webservices.WebService(service)
	application = tornado.httpserver.HTTPServer(ws)
	application.listen(8080)
	tornado.ioloop.IOLoop.instance().start()
