import matplotlib
import mysql.connector
import pandas as pd
import matplotlib.pyplot as plt
matplotlib.use("TkAgg")  # pour affichage graphique

# --- Connexion à MySQL ---
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="root",
    database="cancer_poumon"  # ton nom de base de données
)

# --- Requête SQL pour récupérer les données PM2.5 ---
query = """
SELECT ParentLocation, Period, FactValueNumeric
FROM air_quality_total
WHERE FactValueNumeric IS NOT NULL
ORDER BY ParentLocation, Period
"""

# --- Charger les données dans pandas ---
df_total = pd.read_sql(query, conn)

# --- Conversion et nettoyage des données ---
df_total["FactValueNumeric"] = (
    df_total["FactValueNumeric"]
    .astype(str)
    .str.replace(",", ".", regex=False)
    .astype(float)
)
df_total["Period"] = df_total["Period"].astype(int)

# --- Grouper par région et année (moyenne si doublons) ---
df_grouped = (
    df_total.groupby(["ParentLocation", "Period"])["FactValueNumeric"]
    .mean()
    .reset_index()
)

# --- Création du graphique ---
plt.figure(figsize=(10, 6))
for region in df_grouped["ParentLocation"].unique():
    subset = df_grouped[df_grouped["ParentLocation"] == region]
    plt.plot(subset["Period"], subset["FactValueNumeric"], marker='o', label=region)

plt.xlabel("Année")
plt.ylabel("Concentration PM2.5")
plt.title("Évolution des concentrations PM2.5 par région")
plt.legend(loc="center left", bbox_to_anchor=(1, 0.5))
plt.tight_layout()
plt.subplots_adjust(right=0.8)
plt.grid(True)
plt.show()

# --- Fermer la connexion ---
conn.close()
