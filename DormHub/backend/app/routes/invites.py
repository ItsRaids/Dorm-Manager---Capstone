from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.schemas.invite import InviteCreate, InviteOut, InvitePreview
from app.auth import get_current_user

router = APIRouter(prefix="/invites", tags=["invites"])


@router.post("/households/{household_id}", response_model=InviteOut, status_code=201)
def create_invite(
    household_id: int,
    invite_in: InviteCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    """
    TODO:
    - Confirm current_user is a member (ideally owner) of household_id
    - Generate a unique code (e.g. secrets.token_urlsafe)
    - Set expires_at if the team decides invites should expire
    - Create and return the Invite
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.get("/{code}", response_model=InvitePreview)
def preview_invite(code: str, db: Session = Depends(get_db)):
    """
    TODO:
    - Look up the invite by code
    - 404 if not found; 400/410 if expired or already used
    - Return household name + inviter name so the UI can show
      "You've been invited to join {household_name} by {invited_by_name}"
      before the user commits to accepting
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.post("/{code}/accept", status_code=200)
def accept_invite(code: str, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    """
    TODO:
    - Look up and validate the invite (exists, still pending, not expired)
    - Create a HouseholdMember row for current_user with role="member"
    - Mark the invite as accepted (single-use) — or leave pending if
      the team decides invites are multi-use
    - Return the household so the frontend can redirect to it
    """
    raise HTTPException(status_code=501, detail="Not implemented")
