from flask import Flask, request, jsonify, render_template
import pandas as pd
import numpy as np
from sklearn.neighbors import NearestNeighbors
from sklearn.metrics import precision_score, recall_score, f1_score, accuracy_score, confusion_matrix, precision_recall_curve, auc

app = Flask(__name__)

# Step 1: Load and preprocess the dataset
url = "http://files.grouplens.org/datasets/movielens/ml-100k/u.data"
column_names = ['user_id', 'item_id', 'rating', 'timestamp']
df = pd.read_csv(url, sep='\t', names=column_names)
user_item_matrix = df.pivot(index='user_id', columns='item_id', values='rating').fillna(0)

# Step 2: Implement User-Based Collaborative Filtering
model_knn = NearestNeighbors(metric='cosine', algorithm='brute')
model_knn.fit(user_item_matrix)

# Function to get recommendations
def get_recommendations(user_id, n_recommendations=5):
    distances, indices = model_knn.kneighbors(user_item_matrix.loc[user_id].values.reshape(1, -1), 
                                              n_neighbors=n_recommendations+1)
    rec_item_ids = [user_item_matrix.columns[i] for i in indices.flatten()][1:] 
    return rec_item_ids, distances

# Home page
@app.route('/')
def home():
    return render_template('index.html')

# Get recommendations API
@app.route('/recommend', methods=['POST'])
def recommend():
    user_id = int(request.form['user_id'])
    n_recommendations = int(request.form['n_recommendations'])
    recommendations, _ = get_recommendations(user_id, n_recommendations)
    return jsonify(recommendations=recommendations)

# Evaluation Metrics API
@app.route('/evaluate', methods=['GET'])
def evaluate():
    user_ids = user_item_matrix.index.tolist()
    train_users, test_users = train_test_split(user_ids, test_size=0.2, random_state=42)
    
    y_true, y_pred = [], []
    
    for user in test_users:
        true_items = df[df['user_id'] == user]['item_id'].tolist()
        recommended_items, _ = get_recommendations(user)
        
        for item in user_item_matrix.columns:
            y_true.append(1 if item in true_items else 0)
            y_pred.append(1 if item in recommended_items else 0)

    precision = precision_score(y_true, y_pred)
    recall = recall_score(y_true, y_pred)
    f1 = f1_score(y_true, y_pred)
    accuracy = accuracy_score(y_true, y_pred)
    
    metrics = {
        'precision': precision,
        'recall': recall,
        'f1_score': f1,
        'accuracy': accuracy
    }
    return jsonify(metrics=metrics)

if __name__ == '__main__':
    app.run(debug=True)
