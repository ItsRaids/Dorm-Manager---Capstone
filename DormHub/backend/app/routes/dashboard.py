from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.auth import get_current_user

router = APIRouter(prefix="/dashboard", tags=["dashboard"])


@router.get("")
def get_dashboard(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    """
    TODO:
    - Decide the actual shape of the dashboard response. At minimum for
      Sprint 1 this probably means: the user's household(s), member list,
      and maybe pending invites they've sent.
    - This may end up just being a thin wrapper around
      households.my_households() rather than its own endpoint — worth
      discussing before building it out. Consider whether this endpoint
      is even needed, or if the frontend just calls /households/me directly.
    """
    raise HTTPException(status_code=501, detail="Not implemented")
