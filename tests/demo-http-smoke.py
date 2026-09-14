"""Synthetic HTTP checks restricted to the loopback /demo instance on port 18202."""
from __future__ import annotations
import importlib.util
from pathlib import Path
import sys, json
from http.cookiejar import CookieJar
from urllib.request import build_opener, HTTPCookieProcessor, HTTPRedirectHandler, Request
from urllib.error import HTTPError
from urllib.parse import urlparse, urljoin, urlencode

spec=importlib.util.spec_from_file_location('local_http_parser',Path(__file__).with_name('http-smoke.py'))
assert spec and spec.loader
parser=importlib.util.module_from_spec(spec)
sys.modules[spec.name]=parser
spec.loader.exec_module(parser)
origin='http://127.0.0.1:18202'
def scoped(path):
    url=urljoin(origin,path)
    p=urlparse(url)
    if (p.scheme,p.hostname,p.port)!=('http','127.0.0.1',18202) or not p.path.startswith('/demo/'):
        raise AssertionError('Demo checks refuse non-loopback or unscoped URLs.')
    return url
class Redirects(HTTPRedirectHandler):
    def redirect_request(self,req,fp,code,msg,headers,newurl):
        return super().redirect_request(req,fp,code,msg,headers,scoped(newurl))
cookies=CookieJar()
opener=build_opener(Redirects(),HTTPCookieProcessor(cookies))
def request(path,fields=None):
    req=Request(scoped(path),data=None if fields is None else urlencode(fields).encode(),headers={'User-Agent':'PHP-Ledger-loopback-demo-check/1'})
    try: response=opener.open(req,timeout=15)
    except HTTPError as e: response=e
    with response:return parser.Page(response.status,response.geturl(),response.headers,response.read().decode())
passed=[]
def check(condition,name):
    if not condition:raise AssertionError(name)
    passed.append(name);print('PASS:',name)

entry=request('/demo/')
check(entry.status==200 and '/demo/login' in entry.url,'Demo opens a scoped entry screen')
check('type="password"' not in entry.body,'Public entry does not ask for normal credentials')
check(any(c.name=='phpledger_demo_session' and c.path=='/demo/' and c.has_nonstandard_attr('HttpOnly') for c in cookies),'Demo session has separate name, scoped path and HttpOnly')
form=entry.markup.form_for('/demo/start')
bad=request('/demo/start',form.fields|{'csrf':'invalid','currency':'USD'})
check(bad.status==403,'Public sample creation requires CSRF')
home=request('/demo/start',form.fields|{'currency':'USD'})
check(home.status==200 and urlparse(home.url).path=='/demo/reports','Sample starts on owner reports')
for path,needle in [('/demo/reports/balance-sheet','875.00'),('/demo/reports/profit-loss','875.00'),('/demo/reports/cash-forecast','1,775.00'),('/demo/reports/trial-balance','875.00'),('/demo/pos','Point of sale')]:
    page=request(path)
    check(page.status==200 and needle.lower() in page.body.lower(),path+' renders its real data')
transactions=request('/demo/transactions')
check(transactions.status==200 and 'Harbor Office Supply' in transactions.body,'Private sample transactions are available')
check(all(not href.startswith('/') or href.startswith('/demo/') or href=='/' for href,_ in transactions.markup.links),'Application navigation remains under /demo')
for path in ['/demo/onboarding','/demo/setup/review']:
    check(request(path).status==403,path+' is prohibited server-side')
check(request('/demo/transactions/detail?id=999999').status==403,'Unknown or other visitor records cannot be opened')
check(request('/demo/health').status==200,'Scoped health responds')
logout=transactions.markup.form_for('/demo/logout')
check('/demo/login' in request(logout.action,logout.fields).url,'Leaving the sample returns to demo entry')
check('/demo/login' in request('/demo/reports').url,'Expired visitor cannot read the books')

if '--currencies' in sys.argv:
    for country,currency in [('Malaysia','MYR'),('Bangladesh','BDT'),('Sri Lanka','LKR'),('Nepal','NPR'),('Singapore','SGD')]:
        # New isolated cookie jars create only this check's synthetic visitors; no database reset.
        cookies=CookieJar()
        opener=build_opener(Redirects(),HTTPCookieProcessor(cookies))
        entry=request('/demo/')
        check(entry.status==200 and f'{country} — {currency}' in entry.body,country+' is offered in demo selection')
        start=entry.markup.form_for('/demo/start')
        home=request(start.action,start.fields|{'currency':currency})
        check(home.status==200 and currency in home.body and '875.00' in home.body,currency+' sample starts with its own base currency')
        shop=request('/demo/pos')
        check(shop.status==200 and f'Cash received ({currency})' in shop.body,currency+' POS uses the selected base currency')
        checkout=shop.markup.form_for('/demo/pos/checkout')
        values=checkout.fields|{'cash_received':'20.00','checkout_intent':'record_cash_sale'}
        for key,value in list(values.items()):
            if key.endswith('[sku]'):
                values[key[:-5]+'[quantity]']='2' if value=='NOTE-A5' else '3' if value=='PEN-BLUE' else '0'
        sale=request(checkout.action,values)
        check(sale.status==200 and f'Recorded sale in {currency}' in sale.body and '12.75' in sale.body and '7.25' in sale.body,currency+' checkout preserves exact receipt total and change')
        repeated=request(checkout.action,values)
        check(repeated.status==200 and repeated.url==sale.url,currency+' checkout retry returns the same receipt')
        for path in ['/demo/reports/profit-loss','/demo/reports/balance-sheet']:
            statement=request(path)
            check(statement.status==200 and currency in statement.body and '887.75' in statement.body,currency+' reconciles '+path)
        trial=request('/demo/reports/trial-balance')
        check(trial.status==200 and currency in trial.body and '1,012.75' in trial.body,currency+' trial balance includes the sale once')
        reused=request('/demo/start',{'csrf':checkout.fields['csrf'],'currency':'USD'})
        check(reused.status==200 and currency in reused.body and '887.75' in reused.body,currency+' existing sample ignores a different supported currency on re-entry')
        logout=reused.markup.form_for('/demo/logout')
        check('/demo/login' in request(logout.action,logout.fields).url,currency+' visitor exits cleanly')

print(json.dumps({'passed':len(passed),'failed':0,'target':origin+'/demo/','data':'Synthetic visitor retained until hourly reset.'},indent=2))
