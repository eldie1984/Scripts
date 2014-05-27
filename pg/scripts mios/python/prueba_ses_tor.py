import tornado.ioloop
import tornado.web
import csv

class MainHandler(tornado.web.RequestHandler):
    def get(self):
		sesiones=[]
		for key, val in csv.reader(open("sesiones.csv")):
			sesiones.append([key,val])
	if len(sesiones) > 0 :
		self.render("template.html", title="My title", datos=sesiones)
	else:
		self.write("No rows")


application = tornado.web.Application([
    (r"/", MainHandler),
])

if __name__ == "__main__":
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()