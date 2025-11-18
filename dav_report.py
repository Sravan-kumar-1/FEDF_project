import mysql.connector
import pandas as pd
import matplotlib.pyplot as plt

# ---- CONNECT TO MYSQL ----
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",    # put password if exists
    database="garage_db",
    port=3307       # ✅ your confirmed port
)

print("✅ Connected to Database")

# 1) **Most Booked Services** (using service_name from bookings)
q1 = """
SELECT service_name, COUNT(*) AS total
FROM bookings
GROUP BY service_name
ORDER BY total DESC;
"""
df1 = pd.read_sql(q1, conn)

if not df1.empty:
    df1.plot(kind="bar", x="service_name", y="total", legend=False)
    plt.title("Most Booked Services")
    plt.xlabel("Service Name")
    plt.ylabel("Number of Bookings")
    plt.tight_layout()
    plt.savefig("service_popularity.png")
    plt.show()
    print("✅ Saved: service_popularity.png")
else:
    print("⚠️ No service data found!")

# 2) **Car Model Distribution**
q2 = """
SELECT car_model, COUNT(*) AS total
FROM bookings
GROUP BY car_model
ORDER BY total DESC;
"""
df2 = pd.read_sql(q2, conn)

if not df2.empty:
    df2.set_index("car_model")["total"].plot(kind="pie", autopct="%1.1f%%")
    plt.title("Car Models Distribution")
    plt.ylabel("")
    plt.tight_layout()
    plt.savefig("car_models.png")
    plt.show()
    print("✅ Saved: car_models.png")

# 3) **Bookings Trend Over Time**
q3 = """
SELECT DATE(booking_date) AS day, COUNT(*) AS total
FROM bookings
GROUP BY DATE(booking_date)
ORDER BY day;
"""
df3 = pd.read_sql(q3, conn)

if not df3.empty:
    plt.plot(df3["day"], df3["total"], marker='o')
    plt.title("Bookings Over Time")
    plt.xlabel("Date")
    plt.ylabel("Number of Bookings")
    plt.xticks(rotation=45)
    plt.tight_layout()
    plt.savefig("bookings_trend.png")
    plt.show()
    print("✅ Saved: bookings_trend.png")

# 4) **Status Distribution**
q4 = """
SELECT status, COUNT(*) AS total
FROM bookings
GROUP BY status;
"""
df4 = pd.read_sql(q4, conn)

if not df4.empty:
    df4.set_index("status")["total"].plot(kind="bar")
    plt.title("Booking Status Distribution")
    plt.xlabel("Status")
    plt.ylabel("Count")
    plt.tight_layout()
    plt.savefig("status_distribution.png")
    plt.show()
    print("✅ Saved: status_distribution.png")

conn.close()

print("\n🎉 DAV Analysis Complete — All Charts Saved.\n")
