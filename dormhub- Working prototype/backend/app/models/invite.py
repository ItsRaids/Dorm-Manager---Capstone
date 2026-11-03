from sqlalchemy import Column, Integer, String, Enum, ForeignKey, TIMESTAMP, func
from sqlalchemy.orm import relationship

from app.database import Base


class Invite(Base):
    __tablename__ = "invites"

    id = Column(Integer, primary_key=True, index=True)
    household_id = Column(Integer, ForeignKey("households.id", ondelete="CASCADE"), nullable=False)
    invited_by = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    code = Column(String(32), nullable=False, unique=True, index=True)
    status = Column(
        Enum("pending", "accepted", "expired", "revoked", name="invite_status"),
        nullable=False,
        default="pending",
    )
    expires_at = Column(TIMESTAMP, nullable=True)
    created_at = Column(TIMESTAMP, server_default=func.now())

    household = relationship("Household", back_populates="invites")

    # TODO: decide whether to also store the invited email address here
    # if invites become email-targeted rather than open codes/links.
