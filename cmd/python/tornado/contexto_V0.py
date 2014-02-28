import tornado.ioloop
import tornado.web
from commons import *
import MySQLdb
#import torndb

#class MainHandler(tornado.web.RequestHandler):
#    def get(self):
        #self.write("You requested the main page")
class MainHandler(tornado.web.RequestHandler):
    def get(self):
	db=MySQLdb.connect(host=db_host, user=db_user, passwd=db_pass , db=db_database )
	cursor=db.cursor()
	#db = torndb.Connection(
        #	db_host, db_database,
        #db_user, db_pass)
	#rows = db.query(
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
	if len(rows) > 0 :
		self.render("template.html", title="My title", datos=rows)
	else:
		self.write("No rows")
	cursor.close()
	db.close

class InterfazHandler(tornado.web.RequestHandler):
    def get(self, story_id):
        self.write("Se mostrara la interfaz " + story_id)

application = tornado.web.Application([
    (r"/", MainHandler),
    (r"/interfaz/(facturas|pagos)", InterfazHandler),
])

if __name__ == "__main__":
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()
