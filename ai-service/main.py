from fastapi import FastAPI
from pydantic import BaseModel
from typing import List
from sentence_transformers import SentenceTransformer
from sklearn.metrics.pairwise import cosine_similarity
import logging

app = FastAPI()
logging.basicConfig(level=logging.INFO)

model = SentenceTransformer("paraphrase-multilingual-MiniLM-L12-v2")


class Event(BaseModel):
    id: int
    description: str


class RecommendationRequest(BaseModel):
    user_pet_types: List[str]
    events: List[Event]


@app.post("/recommend")
def recommend(data: RecommendationRequest):

    logging.info("========== NEW AI RECOMMENDATION ==========")

    if not data.user_pet_types:
        return {"recommended_event_ids": []}

    # Build pet concept sentences once
    pet_sentences = [
        f"Ceci est un événement pour {pet.lower()}"
        for pet in data.user_pet_types
    ]

    pet_embeddings = model.encode(pet_sentences)

    recommended_ids = []

    for event in data.events:

        event_embedding = model.encode([event.description])[0]

        similarities = cosine_similarity(
            pet_embeddings, [event_embedding]
        ).flatten()

        best_similarity = max(similarities)

        logging.info(
            f"Event {event.id} | best_pet_similarity={best_similarity:.4f}"
        )

        if best_similarity >= 0.50:
            recommended_ids.append(event.id)

    logging.info(f"Recommended IDs: {recommended_ids}")
    logging.info("==========================================")

    return {"recommended_event_ids": recommended_ids}