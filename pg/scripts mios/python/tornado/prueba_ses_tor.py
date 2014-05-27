from commons import *
import markdown
import os.path
import re
import torndb
import tornado.auth
import tornado.httpserver
import tornado.ioloop
import tornado.options
import tornado.web
import unicodedata
import csv



from tornado.options import define, options

define("port", default=8888, help="run on the given port", type=int)

class Application(tornado.web.Application):
	def __init__(self):
		handlers = [
			(r"/", MainHandler),
		#r"/SES", SesHandler),
		(r"/UPR", UprHandler),
		(r"/running", RunningHandler),
		]
		settings = dict(
			app_title=u"Super hiper mega chequeo",
			template_path=os.path.join(os.path.dirname(__file__), "templates"),
			static_path=os.path.join(os.path.dirname(__file__), "static"),
			xsrf_cookies= False,
			cookie_secret="__TODO:_GENERATE_YOUR_OWN_RANDOM_VALUE_HERE__",
			debug=True,
		)
		tornado.web.Application.__init__(self, handlers, **settings)

		# Have one global connection to the blog DB across all handlers
		
class BaseHandler(tornado.web.RequestHandler):
	@property
	
	def get_current_user(self):
		user_id = self.get_secure_cookie("blogdemo_user")
		if not user_id: return None
		#return self.db.get("SELECT * FROM authors WHERE id = %s", int(user_id))


class MainHandler(BaseHandler):
	def get(self):
		for key, val in csv.reader(open("sesiones.csv")):
			sesiones.append(key)

class InterfazHandler(BaseHandler):
	def get(self, interfaz):
		print tablas[interfaz]
		db_pr.execute("update %s set errormsg='',iserror='N', fecha_envio=null where  iserror='Y'" % tablas[interfaz])
#	   self.db_pr.commit()



class ResetHandler(BaseHandler):
    def post(self):
        try:
            print "Reseteando interfaz"
#            tablas={'CLIENTE':'erp_cliente_ly','PEDIDO':'erp_pedido_ly','FACTURA':'erp_factura_ly','PAGOS':'erp_pago_ly','PRODUCTO':'erp_producto_ly','USUARIO':'erp_usuario_ly'}
            interfaz = self.get_argument("interfaz")
            error = self.get_argument("error")
            if not interfaz or not error:
                return self.write({"success":False})
            if not len(interfaz) or not len(error):
                return self.write({"success":False})
            print "update %s set errormsg='',iserror='N', fecha_envio=null where substring(errormsg from 42 for 150) = '%s' and iserror='Y'" % ( tablas[interfaz], error)
            self.db.execute("update %s set errormsg='',iserror='N', fecha_envio=null where substring(errormsg from 42 for 150) = '%s' and iserror='Y'" , tablas[interfaz], error)
#			self.db_pr.commit()
            self.write({"success":True})
        except Exception,e:
            self.write({"success":False })
	    print e

#application = tornado.web.Application([
#	(r"/", MainHandler),
#	(r"/interfaz/(facturas|pagos)", InterfazHandler),
#])

def main():
	tornado.options.parse_command_line()
	http_server = tornado.httpserver.HTTPServer(Application())
	http_server.listen(options.port)
	tornado.ioloop.IOLoop.instance().start()

if __name__ == "__main__":
	#application.listen(8888)
	#tornado.ioloop.IOLoop.instance().start()
	main()
