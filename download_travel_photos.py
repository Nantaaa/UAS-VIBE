import os, time, requests

BASE_DIR = r"C:\Users\MYPC\Documents\travel-app\storage\app\public"
PAKET_DIR = os.path.join(BASE_DIR, "paket")
DEST_DIR = os.path.join(BASE_DIR, "destinasi")
os.makedirs(PAKET_DIR, exist_ok=True)
os.makedirs(DEST_DIR, exist_ok=True)

# Curated high quality Unsplash photos of Indonesia tourism
PHOTO_MAP = {
    "komodo": "https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?w=1200&auto=format&fit=crop&q=80",
    "bromo": "https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=1200&auto=format&fit=crop&q=80",
    "raja_ampat": "https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?w=1200&auto=format&fit=crop&q=80",
    "nusa_penida": "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200&auto=format&fit=crop&q=80",
    "derawan": "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&auto=format&fit=crop&q=80",
    "sumba": "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80",
    "yogyakarta": "https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=1200&auto=format&fit=crop&q=80",
    "lombok": "https://images.unsplash.com/photo-1570789210967-2cac24afeb00?w=1200&auto=format&fit=crop&q=80",
    "danau_toba": "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&auto=format&fit=crop&q=80",
    "wakatobi": "https://images.unsplash.com/photo-1682687220063-4742bd7fd538?w=1200&auto=format&fit=crop&q=80",
    "belitung": "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80",
    "dieng": "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1200&auto=format&fit=crop&q=80",
    "karimunjawa": "https://images.unsplash.com/photo-1509233725247-49e657c54213?w=1200&auto=format&fit=crop&q=80",
    "bunaken": "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&auto=format&fit=crop&q=80",
    "banda_neira": "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=1200&auto=format&fit=crop&q=80",
    "kawah_ijen": "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=1200&auto=format&fit=crop&q=80",
    "toraja": "https://images.unsplash.com/photo-1513694203232-719a280e022f?w=1200&auto=format&fit=crop&q=80",
    "pulau_seribu": "https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=1200&auto=format&fit=crop&q=80",
    "padang": "https://images.unsplash.com/photo-1516483638261-f4dbaf036963?w=1200&auto=format&fit=crop&q=80",
    "bali_resort": "https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=1200&auto=format&fit=crop&q=80",
    "alor": "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&auto=format&fit=crop&q=80",
    "bromo_midnight": "https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=1200&auto=format&fit=crop&q=80",
}

for key, url in PHOTO_MAP.items():
    p_path = os.path.join(PAKET_DIR, f"{key}.jpg")
    d_path = os.path.join(DEST_DIR, f"{key}.jpg")
    try:
        r = requests.get(url, timeout=15)
        if r.status_code == 200:
            with open(p_path, "wb") as f:
                f.write(r.content)
            with open(d_path, "wb") as f:
                f.write(r.content)
            print(f"Downloaded {key}.jpg ({len(r.content):,} bytes)")
        else:
            print(f"Failed {key}: HTTP {r.status_code}")
    except Exception as e:
        print(f"Error {key}: {e}")

print("All image downloads completed!")
