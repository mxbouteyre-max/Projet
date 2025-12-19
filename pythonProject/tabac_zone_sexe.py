import matplotlib
import mysql.connector
import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
matplotlib.use("TkAgg")


# Connexion MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="root",
    database="cancer_poumon",
    port=3306
)


# 1️ - Charger les fichiers
df_cancer = pd.read_sql("SELECT * FROM cancer", conn)
df_air = pd.read_sql("SELECT * FROM air_quality_total", conn)

# 2️ - Harmonisation des noms de pays (exemple, à compléter selon tes données)
name_corrections = {
    "Türkiye": "Turkey",
    "Cote d'Ivoire": "Côte d'Ivoire",
    "Netherlands (Kingdom of the)": "Netherlands"
}
df_air["Location"] = df_air["Location"].replace(name_corrections)

# 3️ - Supprimer les territoires hors contexte
territories_to_drop = ["Tokelau", "Western Sahara", "Puerto Rico"]
df_air = df_air[~df_air["Location"].isin(territories_to_drop)]
df_cancer = df_cancer[~df_cancer["country_or_territory"].isin(territories_to_drop)]

# 4️ - Nettoyage des colonnes smoking_prevalence
for col in ["smoking_prevalence_male", "smoking_prevalence_female"]:
    df_cancer[col] = df_cancer[col].astype(str).str.replace(",", ".")
    df_cancer[col] = df_cancer[col].replace("No data", np.nan)
    df_cancer[col] = pd.to_numeric(df_cancer[col], errors="coerce")

# 5️ - Fusionner pour récupérer la zone (ParentLocation)
df_merged = pd.merge(
    df_cancer,
    df_air[["ParentLocation", "Location"]],
    left_on="country_or_territory",
    right_on="Location",
    how="left"
)

# 6️ - Calculer la moyenne par zone
df_zone = df_merged.groupby("ParentLocation")[["smoking_prevalence_male","smoking_prevalence_female"]].mean().reset_index()

# 7️ - Graphique comparatif hommes/femmes
plt.figure(figsize=(10,6))
bar_width = 0.4
index = np.arange(len(df_zone))

plt.bar(index, df_zone["smoking_prevalence_male"], bar_width, label="Hommes", color="blue")
plt.bar(index + bar_width, df_zone["smoking_prevalence_female"], bar_width, label="Femmes", color="pink")

plt.xticks(index + bar_width/2, df_zone["ParentLocation"], rotation=45)
plt.ylabel("Prévalence du tabac (%)")
plt.title("Prévalence quotidienne du tabac par zone et par sexe")
plt.legend()
plt.tight_layout()
plt.show()
