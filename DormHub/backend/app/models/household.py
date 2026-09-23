from sqlalchemy import Column, Integer, String, ForeignKey, TIMESTAMP, func
from sqlalchemy.orm import relationship

from app.database import Base


class Household(Base):
    __tablename__ = "households"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(150), nullable=False)
    created_by = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    created_at = Column(TIMESTAMP, server_default=func.now())

    # TODO: household-level settings fields, if any get scoped in

    members = relationship("HouseholdMember", back_populates="household", cascade="all, delete-orphan")
    invites = relationship("Invite", back_populates="household", cascade="all, delete-orphan")
