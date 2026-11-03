from datetime import datetime
from typing import List, Optional

from pydantic import BaseModel, ConfigDict


class HouseholdCreate(BaseModel):
    name: str


class HouseholdOut(BaseModel):
    model_config = ConfigDict(from_attributes=True)

    id: int
    name: str
    created_by: int
    created_at: Optional[datetime] = None


class HouseholdMemberOut(BaseModel):
    model_config = ConfigDict(from_attributes=True)

    user_id: int
    role: str
    joined_at: Optional[datetime] = None
    # TODO: consider nesting user full_name/email here (join query) so the
    # frontend doesn't need a second request per member.


class HouseholdDetailOut(HouseholdOut):
    members: List[HouseholdMemberOut] = []
