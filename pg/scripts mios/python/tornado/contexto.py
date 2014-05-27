from commons import *
import MySQLdb
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



from tornado.options import define, options

define("port", default=8888, help="run on the given port", type=int)
define("mysql_host", default=db_host, help="blog database host")
define("mysql_database", default=db_database, help="blog database name")
define("mysql_user", default=db_user, help="blog database user")
define("mysql_password", default=db_pass, help="blog database password")

tablas={'CLIENTE':'erp_cliente_ly','PEDIDO':'erp_pedido_ly','FACTURA':'erp_factura_ly','PAGOS':'erp_pago_ly','PRODUCTO':'erp_producto_ly','USUARIO':'erp_usuario_ly'}

db_pr = torndb.Connection(
              host=db_host_pr, database=options.mysql_database,
              user=db_user_pr, password=db_pass_pr)

class Application(tornado.web.Application):
	def __init__(self):
		handlers = [
			(r"/", MainHandler),
		(r"/interfaz/(FACTURA|PAGO|CLIENTE|USUARIO|PEDIDO)", InterfazHandler),
		(r"/reset", ResetHandler),
		]
		settings = dict(
			app_title=u"Tablero Libertya",
			template_path=os.path.join(os.path.dirname(__file__), "templates"),
			static_path=os.path.join(os.path.dirname(__file__), "static"),
			xsrf_cookies= False,
			cookie_secret="__TODO:_GENERATE_YOUR_OWN_RANDOM_VALUE_HERE__",
			debug=True,
		)
		tornado.web.Application.__init__(self, handlers, **settings)

		# Have one global connection to the blog DB across all handlers
		self.db = torndb.Connection(
		  host=options.mysql_host, database=options.mysql_database,
		  user=options.mysql_user, password=options.mysql_password)
		self.db_pr = torndb.Connection(
		  host=db_host_pr, database=options.mysql_database,
		  user=db_user_pr, password=db_pass_pr)


class BaseHandler(tornado.web.RequestHandler):
	@property
	def db(self):
		return self.application.db
	def db_pr(self):
		return self.application.db_pr
	def cursor(self):
		return self.application.cursor
	def get_current_user(self):
		user_id = self.get_secure_cookie("blogdemo_user")
		if not user_id: return None
		return self.db.get("SELECT * FROM authors WHERE id = %s", int(user_id))


class MainHandler(BaseHandler):
	def get(self):
#	@tornado.web.asynchronous
#	@tornado.gen.engine
#	def post(self):
#		action = self.get_argument('action')
#		if action == 'reload':
#			logging.info('background job start')
			#yield tornado.gen.Task(tornado.ioloop.IOLoop.instance().add_timeout, time.time() + 3)
			#logging.info('background job stop')
			#self.data["status"] = "reloaded"
			#self.finish("{}")

#		rows = self.db.query("select 'CLIENTE' as 'interfaz', substring(errormsg from 42 for 150) as 'error' ,count(*) as 'cantidad' from erp_cliente_ly where IsError='Y' group by substring(errormsg from 42 for 150)"
#				   " union"
#				  " select 'USUARIO',  substring(errormsg from 42 for 150) end ,count(*) from erp_usuario_ly where IsError='Y'  group by 2"
#				  "  union"
#				  "  select 'PEDIDO', substring(errormsg from 42 for 150),count(*) from erp_pedido_ly where IsError='Y' group by substring(errormsg from 42 for 150)"
#				  "  union"
#				  "  select 'FACTURA', substring(errormsg from 42 for 150),count(*) from erp_factura_ly where IsError='Y' group by substring(errormsg from 42 for 150)"
#				  "  union"
#				  "  select 'PAGOS', substring(errormsg from 42 for 150),count(*) from erp_pago_ly where IsError='Y' group by substring(errormsg from 42 for 150)"
#				  "  union"
#				  "  select 'PRODUCTO', substring(errormsg from 42 for 150),count(*) from erp_producto_ly where IsError='Y' group by substring(errormsg from 42 for 150);"
#				  )
		rows = self.db.query("select 'CLIENTE' as 'interfaz' , substring(errormsg from 42 for 150) as 'error',bp.nombre as portal,count(*) as 'cantidad',null as 'monto' from erp_cliente_ly, bac_portal bp where  ad_org_id=id_organizacion_ly and IsError='Y' group by substring(errormsg from 42 for 150),bp.nombre "
		"union "
		"select 'USUARIO', substring(errormsg from 42 for 150),bp.nombre ,count(*),null from erp_usuario_ly , bac_portal bp where  ad_org_id=id_organizacion_ly and IsError='Y'  group by substring(errormsg from 42 for 150) ,bp.nombre "
		"union "
		"select 'PEDIDO', substring(errormsg from 42 for 150),bp.nombre,count(*),sum(ccl.monto) from erp_pedido_ly epl, bac_portal bp ,cce_lote ccl where  ad_org_id=id_organizacion_ly and epl.id_lote=ccl.id_lote and IsError='Y' group by substring(errormsg from 42 for 150),bp.nombre "
		"union "
		"select 'FACTURA', substring(errormsg from 42 for 150),bp.nombre,count(*),sum(ccl.monto) from erp_factura_ly efl , bac_portal bp,cce_lote ccl where  ad_org_id=id_organizacion_ly and efl.id_lote=ccl.id_lote and efl.IsError='Y' group by substring(errormsg from 42 for 150),bp.nombre "
		"union "
		"select 'PAGOS', substring(errormsg from 42 for 150),bp.nombre,count(*),sum(PayAmt) from erp_pago_ly , bac_portal bp where  ad_org_id=id_organizacion_ly and IsError='Y' group by substring(errormsg from 42 for 150),bp.nombre "
		"union "
		"select 'PRODUCTO', substring(errormsg from 42 for 150),bp.nombre,count(*),null from erp_producto_ly , bac_portal bp where  ad_org_id=id_organizacion_ly and IsError='Y' group by substring(errormsg from 42 for 150),bp.nombre; "
		)
		envios = self.db.query("select 'FACTURA' as 'interfaz',bp.nombre as portal , count(*) as cantidad from erp_factura_ly efl, bac_portal bp where ad_org_id=id_organizacion_ly and fecha_envio is null and efl.id_portal not in  (12) and ad_client_id not in (1973) and c_bpartner_id is not null and id_lote not in (1308482,1328812,1421626,867947,1217041,1228883,1253709,1365738) group by bp.nombre "
			"union  "
			"select 'PEDIDO'  as 'interfaz',bp.nombre as portal, count(*) as cantidad from erp_pedido_ly, bac_portal bp where ad_org_id=id_organizacion_ly and fecha_envio is null group by bp.nombre "
			"union "
			"select 'PAGO' as 'interfaz',bp.nombre as portal, count(*) as cantidad from erp_pago_ly, bac_portal bp where ad_org_id=id_organizacion_ly and fecha_envio is null  group by bp.nombre "
			"union "
			"select 'Cliente' as 'interfaz',bp.nombre as portal, count(*) as cantidad from erp_cliente_ly ecl, bac_portal bp where ad_org_id=id_organizacion_ly and fecha_envio is null and ecl.id_portal not in (12) and ad_client_id not in (1973)  group by bp.nombre "
			"union "
			"select 'usuario' as 'interfaz',bp.nombre as portal, count(*) as cantidad from erp_usuario_ly eul, bac_portal bp where ad_org_id=id_organizacion_ly and fecha_envio is null and eul.id_portal not in (12) and ad_client_id not in (1973)  group by bp.nombre "
			"union "
			"select 'Anulacion' as 'interfaz',bp.nombre as portal, count(*) as cantidad from erp_anulacion_ly eal, bac_portal bp where ad_org_id=id_organizacion_ly and eal.pago_enviado='N'  group by bp.nombre ;")
		
		if not rows :
		  self.write("No rows")	
		else:
		  self.render("home.html", title="My title", datos=rows, resultados = envios)
#		logging.info('background job stop')
#			self.data["status"] = "reloaded"
#			self.finish("{}")

	#cursor.close()
	#db.close

#class InterfazHandler(BaseHandler):
#	def get(self, story_id):
#		self.write("Se mostrara la interfaz " + story_id)

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
