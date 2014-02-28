import urllib2
import urllib
from cookielib import CookieJar

cj = CookieJar()
opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(cj))
# input-type values from the html form
formdata = { "os_username" : "dgasch", "os_password": "Ema84nue!" }
data_encoded = urllib.urlencode(formdata)
response = opener.open("http://jira.int.clarin.com/", data_encoded)
content = response.read()
response2 = opener.open('http://jira.int.clarin.com/browse/SOPOP-24653')
print response2.read()