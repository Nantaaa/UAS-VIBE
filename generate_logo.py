import os

IMG_DIR = r"C:\Users\MYPC\Documents\travel-app\public\images"
os.makedirs(IMG_DIR, exist_ok=True)

# 1. LOGO MARK (Standalone Icon - 64x64)
# Concept: Modern rounded square badge with ocean blue-teal gradient,
# featuring a stylized origami compass / phinisi sail forming letter 'N', plus sparkle star.
svg_mark = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64" fill="none">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0284c7"/>
      <stop offset="50%" stop-color="#0369a1"/>
      <stop offset="100%" stop-color="#0d9488"/>
    </linearGradient>
    <linearGradient id="sailGrad" x1="20%" y1="0%" x2="90%" y2="100%">
      <stop offset="0%" stop-color="#ffffff"/>
      <stop offset="100%" stop-color="#e0f2fe"/>
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#a7f3d0"/>
    </linearGradient>
    <filter id="glow" x="-10%" y="-10%" width="120%" height="120%" filterUnits="userSpaceOnUse">
      <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#0b1b33" flood-opacity="0.25"/>
    </filter>
  </defs>

  <!-- Squircle Base Badge -->
  <rect width="64" height="64" rx="16" fill="url(#bgGrad)"/>
  
  <!-- Subtle Inner Ring Highlight -->
  <rect x="1" y="1" width="62" height="62" rx="15" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/>

  <!-- Stylized Geometric "N" formed by Sail / Compass Arrow -->
  <g filter="url(#glow)">
    <!-- Left Pillar of N (Solid trust bar) -->
    <path d="M16 46V18C16 16.8954 16.8954 16 18 16H21C22.1046 16 23 16.8954 23 18V46C23 47.1046 22.1046 48 21 48H18C16.8954 48 16 47.1046 16 46Z" fill="url(#sailGrad)"/>
    
    <!-- Dynamic Diagonal & Wing of N (Ascending Phinisi Sail & Compass Arrow) -->
    <path d="M21 21L42 45C42.8 45.9 44.2 45.4 44.2 44.2V18C44.2 16.8954 45.0954 16 46.2 16H47C47.5523 16 48 16.4477 48 17V46C48 47.1046 47.1046 48 46 48H43.5C42.4 48 41.3 47.4 40.6 46.5L20 23V21H21Z" fill="url(#sailGrad)"/>
    
    <!-- Right Upward Flight Wedge / Wing -->
    <path d="M23 18L46 34V18C46 16.8954 45.1046 16 44 16H25C23.8954 16 23 16.8954 23 18Z" fill="url(#accentGrad)" opacity="0.9"/>
  </g>

  <!-- Guiding Compass Star (North-East Star) -->
  <path d="M49 13L50.2 15.5L52.7 16.7L50.2 17.9L49 20.4L47.8 17.9L45.3 16.7L47.8 15.5L49 13Z" fill="#fef08a"/>
</svg>'''

# 2. FULL HORIZONTAL LOGO (DARK TEXT for light headers - 210x48)
svg_full_dark = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 210 48" width="210" height="48" fill="none">
  <defs>
    <linearGradient id="bgGradD" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0284c7"/>
      <stop offset="50%" stop-color="#0369a1"/>
      <stop offset="100%" stop-color="#0d9488"/>
    </linearGradient>
    <linearGradient id="sailGradD" x1="20%" y1="0%" x2="90%" y2="100%">
      <stop offset="0%" stop-color="#ffffff"/>
      <stop offset="100%" stop-color="#e0f2fe"/>
    </linearGradient>
    <linearGradient id="accentGradD" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="100%" stop-color="#a7f3d0"/>
    </linearGradient>
  </defs>

  <!-- Icon Mark (38x38) -->
  <g transform="translate(0, 5)">
    <rect width="38" height="38" rx="10" fill="url(#bgGradD)"/>
    <rect x="0.75" y="0.75" width="36.5" height="36.5" rx="9.25" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
    
    <!-- Stylized N -->
    <path d="M10 28V11C10 10.4 10.4 10 11 10H12.5C13.1 10 13.5 10.4 13.5 11V28C13.5 28.6 13.1 29 12.5 29H11C10.4 29 10 28.6 10 28Z" fill="url(#sailGradD)"/>
    <path d="M13 13L25.5 27.5C26 28.1 27 27.7 27 27V11C27 10.4 27.4 10 28 10H28.5C29 10 29.5 10.5 29.5 11V28C29.5 28.6 29 29 28.4 29H26.8C26.1 29 25.5 28.6 25 28L12.5 13.5V13H13Z" fill="url(#sailGradD)"/>
    <path d="M14 11L28 20V11C28 10.4 27.6 10 27 10H15C14.4 10 14 10.4 14 11Z" fill="url(#accentGradD)" opacity="0.85"/>
    <!-- Compass Star -->
    <path d="M30 8L30.7 9.5L32.2 10.2L30.7 10.9L30 12.4L29.3 10.9L27.8 10.2L29.3 9.5L30 8Z" fill="#fef08a"/>
  </g>

  <!-- Typography: NANTA -->
  <text x="48" y="27" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" fill="#0b1b33" letter-spacing="-0.03em">Nanta</text>
  
  <!-- Typography: TRAVEL badge / accent -->
  <text x="110" y="27" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" fill="#0284c7" letter-spacing="-0.02em">Travel</text>
  
  <!-- Tagline -->
  <text x="49" y="38" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="8.5" font-weight="700" fill="#64748b" letter-spacing="0.16em" text-transform="uppercase">Jelajahi Nusantara</text>
</svg>'''

# 3. FULL HORIZONTAL LOGO (WHITE TEXT for dark headers / footer - 210x48)
svg_full_white = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 210 48" width="210" height="48" fill="none">
  <defs>
    <linearGradient id="bgGradW" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#38bdf8"/>
      <stop offset="50%" stop-color="#0284c7"/>
      <stop offset="100%" stop-color="#0d9488"/>
    </linearGradient>
    <linearGradient id="sailGradW" x1="20%" y1="0%" x2="90%" y2="100%">
      <stop offset="0%" stop-color="#ffffff"/>
      <stop offset="100%" stop-color="#e0f2fe"/>
    </linearGradient>
    <linearGradient id="accentGradW" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#7dd3fc"/>
      <stop offset="100%" stop-color="#a7f3d0"/>
    </linearGradient>
  </defs>

  <!-- Icon Mark (38x38) -->
  <g transform="translate(0, 5)">
    <rect width="38" height="38" rx="10" fill="url(#bgGradW)"/>
    <rect x="0.75" y="0.75" width="36.5" height="36.5" rx="9.25" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
    
    <!-- Stylized N -->
    <path d="M10 28V11C10 10.4 10.4 10 11 10H12.5C13.1 10 13.5 10.4 13.5 11V28C13.5 28.6 13.1 29 12.5 29H11C10.4 29 10 28.6 10 28Z" fill="url(#sailGradW)"/>
    <path d="M13 13L25.5 27.5C26 28.1 27 27.7 27 27V11C27 10.4 27.4 10 28 10H28.5C29 10 29.5 10.5 29.5 11V28C29.5 28.6 29 29 28.4 29H26.8C26.1 29 25.5 28.6 25 28L12.5 13.5V13H13Z" fill="url(#sailGradW)"/>
    <path d="M14 11L28 20V11C28 10.4 27.6 10 27 10H15C14.4 10 14 10.4 14 11Z" fill="url(#accentGradW)" opacity="0.85"/>
    <!-- Compass Star -->
    <path d="M30 8L30.7 9.5L32.2 10.2L30.7 10.9L30 12.4L29.3 10.9L27.8 10.2L29.3 9.5L30 8Z" fill="#fef08a"/>
  </g>

  <!-- Typography: NANTA -->
  <text x="48" y="27" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" fill="#ffffff" letter-spacing="-0.03em">Nanta</text>
  
  <!-- Typography: TRAVEL badge / accent -->
  <text x="110" y="27" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" fill="#38bdf8" letter-spacing="-0.02em">Travel</text>
  
  <!-- Tagline -->
  <text x="49" y="38" font-family="Plus Jakarta Sans, Inter, system-ui, -apple-system, sans-serif" font-size="8.5" font-weight="700" fill="rgba(255,255,255,0.7)" letter-spacing="0.16em" text-transform="uppercase">Jelajahi Nusantara</text>
</svg>'''

# Save all SVG assets
with open(os.path.join(IMG_DIR, "logo-mark.svg"), "w", encoding="utf-8") as f:
    f.write(svg_mark)

with open(os.path.join(IMG_DIR, "logo-nanta.svg"), "w", encoding="utf-8") as f:
    f.write(svg_full_dark)

with open(os.path.join(IMG_DIR, "logo-nanta-white.svg"), "w", encoding="utf-8") as f:
    f.write(svg_full_white)

# Save favicon.svg in public root
with open(r"C:\Users\MYPC\Documents\travel-app\public\favicon.svg", "w", encoding="utf-8") as f:
    f.write(svg_mark)

print("All logo vector assets successfully generated!")
