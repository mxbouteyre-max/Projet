import matplotlib
import mysql.connector
import pandas as pd
import matplotlib.pyplot as plt
matplotlib.use("TkAgg")

# Connexion MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="root",
    database="cancer_poumon"
)

# Requête SQL
query = "SELECT year, death_rate_us FROM cancer_evo_usa"

# Charger les données dans pandas
df = pd.read_sql(query, conn)

# Création du graphique
plt.figure(figsize=(10,6))
plt.plot(df["year"], df["death_rate_us"], marker="o", linewidth=2)

plt.title("Évolution du taux de mortalité par cancer du poumon aux États-Unis (1975-2023)")
plt.xlabel("Année")
plt.ylabel("Taux de mortalité (pour 100 000 habitants)")
plt.grid(True, linestyle="--", alpha=0.6)

plt.show()

# Fermer la connexion
conn.close()