from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session

from app.database import get_db
from app.schemas.user import UserCreate, UserOut, Token
from app.auth import get_current_user

router = APIRouter(prefix="/auth", tags=["auth"])


@router.post("/register", response_model=UserOut, status_code=status.HTTP_201_CREATED)
def register(user_in: UserCreate, db: Session = Depends(get_db)):
    """
    TODO:
    - Check whether the email is already registered (return 400 if so)
    - Hash the password with `hash_password` from app.auth
    - Create and save the User
    - Return the created user
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.post("/login", response_model=Token)
def login(form_data: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(get_db)):
    """
    TODO:
    - Look up the user by email (form_data.username holds the email)
    - Verify the password with `verify_password` from app.auth
    - Issue a JWT with `create_access_token({"sub": str(user.id)})`
    - Return {"access_token": token, "token_type": "bearer"}
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.get("/me", response_model=UserOut)
def read_current_user(current_user=Depends(get_current_user)):
    # This one already works once register/login are implemented —
    # get_current_user handles token verification.
    return current_user
