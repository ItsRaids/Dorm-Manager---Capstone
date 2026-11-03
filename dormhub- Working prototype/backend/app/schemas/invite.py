from datetime import datetime
from typing import Optional

from pydantic import BaseModel, ConfigDict


class InviteCreate(BaseModel):
    # TODO: add expires_in_hours or similar if invites should expire
    pass


class InviteOut(BaseModel):
    model_config = ConfigDict(from_attributes=True)

    id: int
    household_id: int
    code: str
    status: str
    expires_at: Optional[datetime] = None
    created_at: Optional[datetime] = None


class InvitePreview(BaseModel):
    """Shown to someone viewing an invite before they accept it."""

    household_id: int
    household_name: str
    invited_by_name: str
    status: str
