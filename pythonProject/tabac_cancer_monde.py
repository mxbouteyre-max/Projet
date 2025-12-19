import pandas as pd
import mysql.connector
import plotly.express as px
from scipy.stats import linregress

# --- Connexion MySQL ---
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="root",
    database="cancer_poumon",
    port=3306
)

# --- Charger les données ---
df = pd.read_sql("SELECT * FROM cancer", conn)

# --- Renommer les colonnes ---
df = df.rename(columns={
    "country_or_territory": "country",
    "smoking_prevalence_male": "Smoking_male",
    "smoking_prevalence_female": "Smoking_female",
    "lung_cancer_incidence_rates_male": "Lung_male",
    "lung_cancer_incidence_rates_female": "Lung_female"
})

# --- Convertir en float et combiner hommes/femmes ---
for col in ["Smoking_male", "Smoking_female", "Lung_male", "Lung_female"]:
    df[col] = pd.to_numeric(df[col], errors="coerce")

df["Smoking_combined"] = df[["Smoking_male", "Smoking_female"]].mean(axis=1)
df["Lung_combined"] = df[["Lung_male", "Lung_female"]].mean(axis=1)

# --- Supprimer les lignes avec NaN ---
df = df.dropna(subset=["Smoking_combined", "Lung_combined"])

# --- Calcul de la régression linéaire ---
slope, intercept, r_value, p_value, std_err = linregress(df["Smoking_combined"], df["Lung_combined"])
df["regression"] = slope * df["Smoking_combined"] + intercept

# --- Création du graphique Plotly ---
fig = px.scatter(
    df,
    x="Smoking_combined",
    y="Lung_combined",
    hover_name="country",
    title="Impact du tabac sur le cancer du poumon",
    labels={
        "Smoking_combined": "Prévalence combinée du tabagisme (%)",
        "Lung_combined": "Incidence combinée du cancer du poumon"
    }
)

# Ajouter la ligne de régression
fig.add_traces(
    px.line(
        df,
        x="Smoking_combined",
        y="regression",
        labels={"y": "Régression linéaire"}
    ).data
)

# --- Export HTML interactif ---
fig.write_html("tabac_cancer_monde_interactif.html", include_plotlyjs="cdn")

# --- Optionnel : afficher directement dans Python ---
fig.show()

print(linregress(df["Smoking_combined"], df["Lung_combined"]))