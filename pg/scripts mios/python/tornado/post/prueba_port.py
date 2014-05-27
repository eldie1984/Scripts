# test.py
import os
import logging
import time

import tornado.httpserver
import tornado.ioloop
import tornado.options
import tornado.web
import tornado.gen

from tornado.options import define, options
define("port", default=8888, help="run on the given port", type=int)

class IndexHandler(tornado.web.RequestHandler):
    def initialize(self, data):
        self.data = data
    def get(self):
        self.render("index.html", data=self.data["status"])
    @tornado.web.asynchronous
    @tornado.gen.engine
    def post(self):
        action = self.get_argument('action')
        if action == 'reload':
            logging.info('background job start')
            yield tornado.gen.Task(tornado.ioloop.IOLoop.instance().add_timeout, time.time() + 3)
            logging.info('background job stop')
            self.data["status"] = "reloaded"
            self.finish("{}")

class Application(tornado.web.Application):
    def __init__(self):
        self.data = {"status":"initialized"}
        handlers = [
            (r"/", IndexHandler, {"data":self.data}),
        ]
        settings = dict(
            static_path = 'static',
            template_path=os.path.join(os.path.dirname(__file__), "templates"),
        )
        tornado.web.Application.__init__(self, handlers, **settings)

if __name__ == "__main__":
    tornado.options.parse_command_line()
    app = Application()
    http_server = tornado.httpserver.HTTPServer(app)
    http_server.listen(options.port)
    tornado.ioloop.IOLoop.instance().start()
