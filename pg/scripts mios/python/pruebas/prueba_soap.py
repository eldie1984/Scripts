import SOAPpy, getpass, datetime

soap = SOAPpy.WSDL.Proxy('http://yval6930:12680/SM/7/IncidentManagement.wsdl?wsdl')

auth = soap.login('e449806', 'Ema84nue')