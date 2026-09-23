from typing import List

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session

from app.database import get_db
from app.schemas.household import HouseholdCreate, HouseholdOut, HouseholdDetailOut
from app.auth import get_current_user

router = APIRouter(prefix="/households", tags=["households"])


@router.post("", response_model=HouseholdOut, status_code=201)
def create_household(
    household_in: HouseholdCreate,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    """
    TODO:
    - Create the Household (created_by = current_user.id)
    - Also create a HouseholdMember row for current_user with role="owner"
    - Decide: if a user can only belong to one household, reject if they
      already belong to one
    - Return the created household
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.get("/me", response_model=List[HouseholdDetailOut])
def my_households(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    """
    TODO:
    - Look up HouseholdMember rows for current_user
    - Load each associated Household (with its members) and return them
    - This likely backs the dashboard's household info + member list
    """
    raise HTTPException(status_code=501, detail="Not implemented")


@router.get("/{household_id}", response_model=HouseholdDetailOut)
def get_household(household_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    """
    TODO:
    - Fetch the household by id
    - Confirm current_user is a member (403 if not)
    - Return household + members
    """
    raise HTTPException(status_code=501, detail="Not implemented")
