#!/usr/bin/env python3
"""
Simple build script for Minecraft Inside website
"""

import os
import shutil
from pathlib import Path
import argparse
import subprocess

class SimpleBuildSystem:
    def __init__(self):
        self.base_dir = Path(__file__).parent
        print(f"Working in: {self.base_dir}")
        
    def find_sass(self):
        """Find sass executable"""
        # Try different possible locations
        possible_paths = [
            'sass',
            'sass.cmd',  # Windows
            r'C:\Program Files\nodejs\sass.cmd',
            r'C:\Users\{}\AppData\Roaming\npm\sass.cmd'.format(os.getenv('USERNAME')),
        ]
        
        for path in possible_paths:
            try:
                result = subprocess.run([path, '--version'], capture_output=True, text=True)
                if result.returncode == 0:
                    print(f"Found sass at: {path}")
                    return path
            except:
                continue
        
        return None
    
    def compile_scss(self):
        """Try to compile SCSS to CSS"""
        scss_dir = self.base_dir / 'scss'
        css_dir = self.base_dir / 'css'
        
        print(f"Looking for SCSS in: {scss_dir}")
        
        if not scss_dir.exists():
            print("SCSS directory not found!")
            return False
            
        # Find sass executable
        sass_path = self.find_sass()
        if not sass_path:
            print("Sass not found. Please make sure it's installed and in PATH")
            print("Install with: npm install -g sass")
            return False
        
        try:
            print("Compiling SCSS with sass...")
            
            input_file = scss_dir / 'main.scss'
            output_file = css_dir / 'minecraft-style.css'
            
            if not input_file.exists():
                print("main.scss not found!")
                return False
            
            result = subprocess.run([
                sass_path,
                '--style=expanded',
                str(input_file),
                str(output_file)
            ], capture_output=True, text=True)
            
            if result.returncode == 0:
                print("SCSS compiled successfully!")
                print(f"Output: {output_file}")
                return True
            else:
                print(f"SCSS compilation failed: {result.stderr}")
                return False
                
        except Exception as e:
            print(f"Sass execution error: {e}")
            return False
    
    def build(self, production=False):
        """Main build process"""
        print("Starting build process...")
        print("======================================")
        
        # Compile SCSS
        success = self.compile_scss()
        
        if success:
            print("Build completed successfully!")
        else:
            print("Build completed with warnings")
        
        return True

def main():
    print("Minecraft Inside Build System")
    print("================================")
    
    parser = argparse.ArgumentParser(description='Build System for Minecraft Inside')
    parser.add_argument('--production', action='store_true', help='Create production build')
    
    args = parser.parse_args()
    
    builder = SimpleBuildSystem()
    builder.build(args.production)

if __name__ == '__main__':
    main()