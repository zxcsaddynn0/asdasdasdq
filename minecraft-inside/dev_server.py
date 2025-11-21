#!/usr/bin/env python3
"""
Simple development server for Minecraft Inside
"""

import http.server
import socketserver
import webbrowser
import os
from pathlib import Path

class DevServer:
    def __init__(self, port=8000):
        self.port = port
        self.base_dir = Path(__file__).parent
        
    def start_server(self):
        """Start the development server"""
        handler = http.server.SimpleHTTPRequestHandler
        
        # Change to project directory
        os.chdir(self.base_dir)
        
        print(f"Starting server on port {self.port}...")
        print(f"Serving from: {self.base_dir}")
        
        with socketserver.TCPServer(("", self.port), handler) as httpd:
            print(f"Development server running at: http://localhost:{self.port}")
            print("Open this URL in your browser")
            print("Press Ctrl+C to stop the server")
            print("======================================")
            
            # Open browser automatically
            try:
                webbrowser.open(f'http://localhost:{self.port}')
            except:
                print(f"Please open: http://localhost:{self.port}")
            
            try:
                httpd.serve_forever()
            except KeyboardInterrupt:
                print("\nServer stopped")

if __name__ == '__main__':
    print("Minecraft Inside Development Server")
    print("======================================")
    server = DevServer(port=8000)
    server.start_server()