import string
import csv

for key, val in csv.reader(open("sesiones.csv")):
    print key , filter(lambda x: x in string.printable, val)
