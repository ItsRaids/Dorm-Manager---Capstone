import os

from dotenv import load_dotenv
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.database import Base, engine
from app.routes import auth, households, invites, dashboard

load_dotenv()

# Creates tables if they don't already exist. For real migrations, prefer
# Alembic instead of relying on this in production.
Base.metadata.create_all(bind=engine)

app = FastAPI(title="Dormhub API")

allowed_origins = os.getenv("ALLOWED_ORIGINS", "http://localhost:5500").split(",")

app.add_middleware(
    CORSMiddleware,
    allow_origins=allowed_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(auth.router)
app.include_router(households.router)
app.include_router(invites.router)
app.include_router(dashboard.router)


@app.get("/")
def root():
    return {"message": "Dormhub API is running"}
