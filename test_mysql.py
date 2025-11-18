import mysql.connector

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",   # if you use a DB password, put it here
        database="garage_db",
        port=3307      # ⬅️ VERY IMPORTANT — if unsure, tell me
    )
    print("✅ Database Connected Successfully!")
    conn.close()
except Exception as e:
    print("❌ Connection Failed:", e)
