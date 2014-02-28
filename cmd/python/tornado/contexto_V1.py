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

class Application(tornado.web.Application):
    def __init__(self):
        handlers = [
            (r"/", MainHandler),
	    (r"/interfaz/(FACTURA|PAGO)", InterfazHandler),
        ]
        settings = dict(
            app_title=u"Tablero Libertya",
            template_path=os.path.join(os.path.dirname(__file__), "templates"),
            static_path=os.path.join(os.path.dirname(__file__), "static"),
#            ui_modules={"Entry": EntryModule},
            xsrf_cookies=True,
            cookie_secret="__TODO:_GENERATE_YOUR_OWN_RANDOM_VALUE_HERE__",
        #    login_url="/auth/login",
            debug=True,
        )
        tornado.web.Application.__init__(self, handlers, **settings)

        # Have one global connection to the blog DB across all handlers
        self.db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	self.cursor=self.db.cursor()
	self.tablas={'CLIENTE':'erp_cliente_ly','PEDIDO':'erp_pedido_ly','FACTURA':'erp_factura_ly','PAGOS':'erp_pago_ly','PRODUCTO':'erp_producto_ly','USUARIO':'erp_usuario_ly'}

class BaseHandler(tornado.web.RequestHandler):
    @property
    def db(self):
        return self.application.db
    def cursor(self):
    	return self.application.cursor
    def get_current_user(self):
        user_id = self.get_secure_cookie("blogdemo_user")
        if not user_id: return None
        return self.db.get("SELECT * FROM authors WHERE id = %s", int(user_id))


class MainHandler(BaseHandler):
    def get(self):
	#db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	#cursor=db.cursor()
	#db = torndb.Connection(
        #	db_host, db_database,
        #db_user, db_pass)
	#rows = db.query(
	#cursor.execute(
#	rows=[("a","b","1")]
#	self.render("template.html", title="My title", datos=rows)	
#    @tornado.web.asynchronous
#    @tornado.gen.engine
#    def post(self):
#        action = self.get_argument('action')
#        if action == 'reload':
#            logging.info('background job start')
            #yield tornado.gen.Task(tornado.ioloop.IOLoop.instance().add_timeout, time.time() + 3)
            #logging.info('background job stop')
            #self.data["status"] = "reloaded"
            #self.finish("{}")

	    cursor=self.db.cursor()
	    cursor.execute("""select 'CLIENTE', substring(errormsg from 42 for 150),count(*) from erp_cliente_ly where IsError='Y' group by substring(errormsg from 42 for 150)
                    union
                    select 'USUARIO', if (substring(errormsg from 42 for 150) like '%Ya existe un contacto%','Ya existe un contacto para la Entidad Comercial',substring(errormsg from 42 for 150)) ,count(*) from erp_usuario_ly where IsError='Y'  group by if (substring(errormsg from 42 for 150) like '%Ya existe un contacto%','Ya existe un contacto para la Entidad Comercial',substring(errormsg from 42 for 150))
                    union
                    select 'PEDIDO', substring(errormsg from 42 for 150),count(*) from erp_pedido_ly where IsError='Y' group by substring(errormsg from 42 for 150)
                    union
                    select 'FACTURA', substring(errormsg from 42 for 150),count(*) from erp_factura_ly where IsError='Y' group by substring(errormsg from 42 for 150)
                    union
                    select 'PAGOS', substring(errormsg from 42 for 150),count(*) from erp_pago_ly where IsError='Y' group by substring(errormsg from 42 for 150)
                    union
                    select 'PRODUCTO', substring(errormsg from 42 for 150),count(*) from erp_producto_ly where IsError='Y' group by substring(errormsg from 42 for 150);
                        """)
	    rows = cursor.fetchall()
#	print rows
	    if not rows :
		self.write("No rows")	
	    else:
		self.render("template.html", title="My title", datos=rows)
#	    logging.info('background job stop')
#            self.data["status"] = "reloaded"
#            self.finish("{}")

	#cursor.close()
	#db.close

#class InterfazHandler(BaseHandler):
#    def get(self, story_id):
#        self.write("Se mostrara la interfaz " + story_id)

class InterfazHandler(BaseHandler):
    def get(self, interfaz):
	tablas={'CLIENTE':'erp_cliente_ly','PEDIDO':'erp_pedido_ly','FACTURA':'erp_factura_ly','PAGOS':'erp_pago_ly','PRODUCTO':'erp_producto_ly','USUARIO':'erp_usuario_ly'}
        cursor=self.db.cursor()
        cursor.execute("update %s set errormsg='',iserror='N', fecha_envio=null where  iserror='Y'" % tablas[interfaz])
#       self.db_pr.commit()



class ResultsHandler(BaseHandler):
    def post(self):
        try:
            print "Adding new book"
            interfaz = self.get_argument("interfaz")
            error = self.get_argument("error")
            if not interfaz or not error:
                return self.write({"success":False})
            if not len(interfaz) or not len(error):
                return self.write({"success":False})
            cursor=self.db.cursor()
	    cursor.execute("update %s set errormsg='',iserror='N', fecha_envio=null where errormsg like '%s' and iserror='Y'" % (self.tablas[interfaz], '%'+error+'%'))
#            self.db_pr.commit()
	    self.write({"success":True})
        except:
            self.write({"success":False})

#application = tornado.web.Application([
#    (r"/", MainHandler),
#    (r"/interfaz/(facturas|pagos)", InterfazHandler),
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
