import sys
import tabula
import os
import time
ts = time.time()

pdffile=sys.argv[1]
pdfpages=sys.argv[2]

base=os.path.basename(pdffile)
dirpath=os.path.dirname(pdffile)
ocrpdf=dirpath+'/'+str(ts)+'-'+base
csv=sys.argv[3]


if pdfpages == 'all':
    os.system('ocrmypdf -l eng --deskew --force-ocr {} {}'.format(pdffile,ocrpdf))
else:
    os.system('qpdf --empty --pages {} {} -- {}'.format(pdffile,pdfpages,ocrpdf))
    os.system('ocrmypdf -l eng --deskew --force-ocr {} {}'.format(ocrpdf,ocrpdf))

tabula.convert_into(ocrpdf, csv, output_format="csv",pages='all')
os.remove(ocrpdf)
