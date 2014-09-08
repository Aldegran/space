#!/usr/bin/python
# -*- coding: UTF-8 -*-

import sys
import argparse
import pycurl
from ConfigParser import SafeConfigParser

def createParser ():
    parser = argparse.ArgumentParser()
    subparsers = parser.add_subparsers (dest='command')

    subparsers.add_parser ('version')

    server_parser = subparsers.add_parser ('server')
    server_parser.add_argument ('url', nargs='?', default=False)

    test_parser = subparsers.add_parser ('test')
    test_parser.add_argument ('-d', '--db', action='store_true', default=False)

    debug_parser = subparsers.add_parser ('debug')
    debug_parser.add_argument ('-s', '--session', action='store_true', default=False)
    debug_parser.add_argument ('--dbq', action='store_true', default=False)
    debug_parser.add_argument ('-r', '--request', action='store_true', default=False)

    user_parser = subparsers.add_parser ('user')
    user_parser.add_argument ('-n','--name', nargs='+', default=False)
    user_parser.add_argument ('-l', '--list', action='store_true', default=False)
    user_parser.add_argument ('-o', '--logout', action='store_true', default=False)
    user_parser.add_argument ('-p', '--password', nargs='?', default=False)
    user_parser.add_argument ('-d', '--delete', action='store_true', default=False)

    my_parser = subparsers.add_parser ('my')
    my_parser.add_argument ('info', nargs='?', choices=['account', 'planets'], default='account')
    my_parser.add_argument ('-v', '--verbose', action='store_true', default=False)

    planet_parser = subparsers.add_parser ('planet')
    planet_parser.add_argument ('name', nargs='?', default=False)
    planet_parser.add_argument ('-l', '--list', action='store_true', default=False)
    planet_parser.add_argument ('-u', '--use', action='store_true', default=False)
    #planet_parser.add_argument ('-i', '--info', action='store_true', default=False)
    planet_parser.add_argument ('-n', '--near', action='store_true', default=False)
    planet_parser.add_argument ('-c', '--create', nargs='+', default=False)
    planet_parser.add_argument ('--all', action='store_true', default=False)
    planet_parser.add_argument ('-t', '--task', nargs='?', default=False)
    planet_parser.add_argument ('-s', '--study', nargs='?', default=False)
    planet_parser.add_argument ('-k', '--knows', action='store_true', default=False)
    planet_parser.add_argument ('-a', '--available', action='store_true', default=False)
    planet_parser.add_argument ('-v', '--verbose', action='store_true', default=False)

    science_parser = subparsers.add_parser ('science')
    science_parser.add_argument ('name', nargs='?', default=False)
    science_parser.add_argument ('-a', '--all', action='store_true', default=False)
    science_parser.add_argument ('-c', '--check', nargs='?', default=False)
    science_parser.add_argument ('-v', '--verbose', action='store_true', default=False)


    register_parser = subparsers.add_parser ('register')
    register_parser.add_argument ('-l', '--login', required=True)
    register_parser.add_argument ('-p', '--password', required=True)
    register_parser.add_argument ('-n', '--name', nargs='+', required=True)

    #curl_parser = subparsers.add_parser ('net')
    #curl_parser.add_argument ('-u', '--url', action='store_true', default=False)

    return parser


def run_version (namespace):
    print ("Version: 0.0.0.0.0.0.1")


def run_server (namespace):
    cfgParser = SafeConfigParser()
    cfgParser.read("sp.conf")
    if namespace.url:
        cfgParser.set('connection', 'url', namespace.url)
        with open("sp.conf", 'wb') as configfile:
            cfgParser.write(configfile)
        print ("Set server:\t" + namespace.url)
    else :
        print ("Current server:\t" + cfgParser.get('connection','url'))


def write_data( buf ):
    print (buf)

def save_session(session):
    cfgParser = SafeConfigParser()
    cfgParser.read("sp.conf")
    cfgParser.set('connection', 'session', session)
    with open("sp.conf", 'wb') as configfile:
        cfgParser.write(configfile)

def write_data_login(buf):
    if buf.startswith('>'):
        save_session(buf[1:])
        print ("Access success\n")    
    else:
        print (buf)

def run_curl (namespace, writeFunction):
    p = [];
    cfgParser = SafeConfigParser()
    cfgParser.read("sp.conf")
    for i in dir(namespace):
        if not i.startswith('_') and i in namespace:
            if(type(getattr(namespace,i)) is type([])):
                p.append(i + "="+ "%20".join(getattr(namespace,i)))
            else: 
                p.append(i + "="+ str(getattr(namespace,i)))

    c = pycurl.Curl()
    c.setopt( pycurl.URL, cfgParser.get('connection','url') + "?" + "&".join(p) )
    c.setopt( pycurl.WRITEFUNCTION, writeFunction )
    if(cfgParser.get('connection','session') != "0"):
        c.setopt( pycurl.COOKIE, "PHPSESSID=" + cfgParser.get('connection','session')) 
    c.perform()

    c.close()

if __name__ == '__main__':
    parser = createParser()
    namespace = parser.parse_args(sys.argv[1:])
    #print('\033[34m')
    #print (namespace),
    #print('\033[0m')

    if namespace.command == "version":
        run_version (namespace)
    elif namespace.command == "server":
        run_server (namespace)
    elif namespace.command == "user":
        if namespace.password and namespace.name:
            run_curl (namespace, write_data_login)
        elif namespace.logout:
            run_curl (namespace, write_data)
            save_session("0");
        else:
            run_curl (namespace, write_data)
    else:
        run_curl (namespace, write_data)
